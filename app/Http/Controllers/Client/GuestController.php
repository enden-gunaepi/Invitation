<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\InvitationAccessService;
use App\Services\InvitationFunnelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class GuestController extends Controller
{
    public function __construct(private readonly InvitationAccessService $invitationAccessService)
    {
    }

    public function index(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        $search = trim((string) $request->query('q', ''));

        $guests = $invitation->guests()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($guestQuery) use ($search) {
                    $guestQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%')
                        ->orWhere('seat_label', 'like', '%' . $search . '%');
                });
            })->when($search !== '', function ($query) use ($search) {
                $query->orderByRaw(
                    "CASE
                        WHEN name LIKE ? THEN 0
                        WHEN name LIKE ? THEN 1
                        WHEN phone LIKE ? THEN 2
                        WHEN email LIKE ? THEN 3
                        WHEN category LIKE ? THEN 4
                        WHEN seat_label LIKE ? THEN 5
                        ELSE 6
                    END",
                    [
                        $search,
                        $search . '%',
                        $search . '%',
                        $search . '%',
                        $search . '%',
                        $search . '%',
                    ]
                );
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
        $invitation->load('package');

        $maxGuests = $invitation->package->max_guests ?? 100;
        $currentGuests = $invitation->guests()->count();
        $checkedInGuests = $invitation->guests()->whereNotNull('checked_in_at')->count();
        $seatAssignedGuests = $invitation->guests()->whereNotNull('table_number')->count();

        if ($request->ajax()) {
            return response()->json([
                'list_html' => View::make('client.guests.partials.list', compact('guests', 'invitation', 'search'))->render(),
                'pagination_html' => $guests->links()->toHtml(),
            ]);
        }

        return view('client.guests.index', compact('invitation', 'guests', 'maxGuests', 'currentGuests', 'checkedInGuests', 'seatAssignedGuests', 'search'));
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        // Enforce package guest limit
        $invitation->load('package');
        $maxGuests = $invitation->package->max_guests ?? 100;
        $currentGuests = $invitation->guests()->count();

        if ($currentGuests >= $maxGuests) {
            return redirect()->route('client.invitations.guests.index', $invitation)
                ->with('error', "Batas tamu untuk paket {$invitation->package->name} adalah {$maxGuests} orang. Upgrade paket untuk menambah tamu.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'category' => 'nullable|string',
            'pax' => 'required|integer|min:1|max:10',
        ]);

        $validated['invitation_id'] = $invitation->id;

        Guest::create($validated);

        return redirect()->route('client.invitations.guests.index', $invitation)
            ->with('success', "Tamu berhasil ditambahkan! ({$currentGuests}/{$maxGuests})");
    }

    public function destroy(Invitation $invitation, Guest $guest)
    {
        $this->authorize($invitation);

        $guest->delete();

        return redirect()->route('client.invitations.guests.index', $invitation)
            ->with('success', 'Tamu berhasil dihapus!');
    }

    public function checkin(Invitation $invitation)
    {
        $this->authorize($invitation);

        $checkedIn = $invitation->guests()->whereNotNull('checked_in_at')->count();
        $total = $invitation->guests()->count();

        return view('client.guests.checkin', compact('invitation', 'checkedIn', 'total'));
    }

    public function checkinScan(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        $validated = $request->validate([
            'token' => 'required|string|min:10',
        ]);

        $token = trim($validated['token']);
        if (str_contains($token, '/')) {
            $parts = explode('/', trim($token, '/'));
            $token = end($parts);
        }

        $guest = Guest::resolveForInvitation((int) $invitation->id, $token);
        if (!$guest) {
            return back()->with('error', 'Guest token tidak ditemukan.');
        }

        if ($guest->checked_in_at) {
            return back()->with('success', "Tamu {$guest->name} sudah check-in pada {$guest->checked_in_at->format('H:i')}.");
        }

        $guest->update([
            'checked_in_at' => now(),
            'checkin_method' => 'qr',
            'checked_in_by_user_id' => auth()->id(),
        ]);

        app(InvitationFunnelService::class)->track((int) $invitation->id, 'checked_in', [
            'guest_id' => $guest->id,
            'guest_token' => $guest->token,
            'phone' => $guest->phone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'source' => 'checkin_scan',
        ]);

        return back()->with('success', "Check-in berhasil: {$guest->name}");
    }

    public function autoSeatAssign(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        $validated = $request->validate([
            'seats_per_table' => 'required|integer|min:2|max:20',
            'start_table' => 'nullable|integer|min:1|max:999',
        ]);

        $seatsPerTable = (int) $validated['seats_per_table'];
        $tableNo = (int) ($validated['start_table'] ?? 1);
        $seatIndex = 1;
        $updated = 0;

        $guests = $invitation->guests()->orderBy('id')->get();
        foreach ($guests as $guest) {
            if ($seatIndex > $seatsPerTable) {
                $tableNo++;
                $seatIndex = 1;
            }

            $guest->update([
                'table_number' => $tableNo,
                'seat_label' => 'S' . str_pad((string) $seatIndex, 2, '0', STR_PAD_LEFT),
            ]);

            $seatIndex++;
            $updated++;
        }

        return back()->with('success', "Auto seating plan selesai. {$updated} tamu ditempatkan.");
    }

    public function import(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        $request->validate([
            'guest_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
        ]);

        $invitation->load('package');
        $maxGuests = $invitation->package->max_guests ?? 100;
        $currentGuests = $invitation->guests()->count();
        $remaining = max(0, $maxGuests - $currentGuests);

        if ($remaining <= 0) {
            return redirect()->route('client.invitations.guests.index', $invitation)
                ->with('error', "Batas tamu paket {$invitation->package->name} sudah penuh ({$maxGuests}).");
        }

        try {
            $sheet = IOFactory::load($request->file('guest_file')->getRealPath())->getActiveSheet();
            $rows = $this->normalizeImportedRows($sheet->toArray('', true, true, false));
        } catch (Throwable $e) {
            return redirect()->route('client.invitations.guests.index', $invitation)
                ->with('error', 'File tidak bisa dibaca. Pastikan format Excel/CSV valid.');
        }

        if (count($rows) < 1) {
            return redirect()->route('client.invitations.guests.index', $invitation)
                ->with('error', 'File kosong. Tidak ada data tamu untuk diimpor.');
        }

        $headerMap = $this->buildHeaderMap($rows[0] ?? []);
        $startRow = isset($headerMap['name']) ? 1 : 0;

        if (!isset($headerMap['name'])) {
            $headerMap = [
                'name' => 0,
                'phone' => 1,
                'email' => 2,
                'category' => 3,
                'pax' => 4,
                'notes' => 5,
            ];
        }

        $existingKeys = $invitation->guests()
            ->get(['name', 'phone'])
            ->map(fn ($g) => mb_strtolower(trim(($g->name ?? '') . '|' . ($g->phone ?? ''))))
            ->flip();

        $rowsToCreate = [];
        $seen = [];
        $skipped = 0;

        for ($i = $startRow; $i < count($rows); $i++) {
            $row = $rows[$i] ?? [];
            $guest = $this->mapGuestRow($row, $headerMap);

            if (!$guest) {
                $skipped++;
                continue;
            }

            $key = mb_strtolower(trim(($guest['name'] ?? '') . '|' . ($guest['phone'] ?? '')));
            if ($existingKeys->has($key) || isset($seen[$key])) {
                $skipped++;
                continue;
            }

            $seen[$key] = true;
            $guest['invitation_id'] = $invitation->id;
            $rowsToCreate[] = $guest;
        }

        if (empty($rowsToCreate)) {
            return redirect()->route('client.invitations.guests.index', $invitation)
                ->with('error', 'Tidak ada data tamu valid untuk diimpor.');
        }

        if (count($rowsToCreate) > $remaining) {
            $rowsToCreate = array_slice($rowsToCreate, 0, $remaining);
            $skipped++;
        }

        foreach ($rowsToCreate as $guestData) {
            Guest::create($guestData);
        }

        $imported = count($rowsToCreate);
        $newCount = $currentGuests + $imported;

        return redirect()->route('client.invitations.guests.index', $invitation)
            ->with('success', "Import selesai: {$imported} tamu ditambahkan, {$skipped} baris dilewati. ({$newCount}/{$maxGuests})");
    }

    private function buildHeaderMap(array $headerRow): array
    {
        $aliases = [
            'name' => ['name', 'nama', 'nama_tamu', 'guest_name'],
            'phone' => ['phone', 'telepon', 'telp', 'hp', 'no_hp', 'nomor_hp', 'wa', 'whatsapp'],
            'email' => ['email', 'mail'],
            'category' => ['category', 'kategori', 'group', 'grup'],
            'pax' => ['pax', 'jumlah', 'jumlah_tamu', 'jumlah_kursi', 'kursi'],
            'notes' => ['notes', 'catatan', 'note', 'keterangan'],
        ];

        $map = [];
        foreach ($headerRow as $idx => $head) {
            $normalized = $this->normalizeHeader((string) $head);
            foreach ($aliases as $field => $candidates) {
                if (in_array($normalized, $candidates, true)) {
                    $map[$field] = $idx;
                    break;
                }
            }
        }

        return $map;
    }

    private function normalizeImportedRows(array $rows): array
    {
        return array_map(function ($row) {
            if (!is_array($row)) {
                return [];
            }

            $trimmed = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $row);
            $nonEmpty = array_values(array_filter($trimmed, fn ($value) => trim((string) $value) !== ''));

            if (count($nonEmpty) === 1) {
                $single = (string) $nonEmpty[0];
                $delimiters = [',', ';', "\t"];

                foreach ($delimiters as $delimiter) {
                    if (str_contains($single, $delimiter)) {
                        return array_map('trim', str_getcsv($single, $delimiter));
                    }
                }
            }

            return $trimmed;
        }, $rows);
    }

    private function normalizeHeader(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/u', '_', $text) ?? '';
        return trim($text, '_');
    }

    private function mapGuestRow(array $row, array $map): ?array
    {
        $name = trim((string) ($row[$map['name']] ?? ''));
        $phone = trim((string) ($row[$map['phone']] ?? ''));
        $email = trim((string) ($row[$map['email']] ?? ''));
        $category = trim((string) ($row[$map['category']] ?? ''));
        $notes = trim((string) ($row[$map['notes']] ?? ''));
        $paxRaw = trim((string) ($row[$map['pax']] ?? ''));

        if ($name === '') {
            return null;
        }

        if (mb_strlen($name) > 100) {
            $name = mb_substr($name, 0, 100);
        }
        if ($phone !== '' && mb_strlen($phone) > 20) {
            $phone = mb_substr($phone, 0, 20);
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = '';
        }

        $pax = (int) $paxRaw;
        if ($pax < 1) {
            $pax = 1;
        } elseif ($pax > 10) {
            $pax = 10;
        }

        return [
            'name' => $name,
            'phone' => $phone ?: null,
            'email' => $email ?: null,
            'category' => $category ?: null,
            'notes' => $notes ?: null,
            'pax' => $pax,
        ];
    }

    private function authorize(Invitation $invitation)
    {
        if (!$this->invitationAccessService->isOwnerOrEditor($invitation, (int) auth()->id())) {
            abort(403);
        }
    }
}
