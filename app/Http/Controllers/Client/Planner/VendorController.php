<?php

namespace App\Http\Controllers\Client\Planner;

use App\Http\Controllers\Controller;
use App\Models\Planner\WpProfile;
use App\Models\Planner\WpVendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $profile = $this->getProfile();
        $query = $profile->vendors();

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $vendors = $query->latest()->get();

        $stats = [
            'total' => $profile->vendors()->count(),
            'prospek' => $profile->vendors()->where('status', 'prospek')->count(),
            'deal' => $profile->vendors()->where('status', 'deal')->count(),
            'dp_paid' => $profile->vendors()->where('status', 'dp_paid')->count(),
            'lunas' => $profile->vendors()->where('status', 'lunas')->count(),
            'cancelled' => $profile->vendors()->where('status', 'cancelled')->count(),
            'total_cost' => (float) $profile->vendors()->whereIn('status', ['deal', 'dp_paid', 'lunas'])->sum('price'),
            'total_paid' => (float) $profile->vendors()->sum('dp_amount')
                + (float) $profile->vendors()->whereNotNull('remaining_paid_at')->sum('remaining_amount'),
        ];

        $categoryOptions = WpVendor::categoryOptions();
        $statusOptions = WpVendor::statusOptions();

        return view('client.planner.vendors.index', compact(
            'profile', 'vendors', 'stats', 'categoryOptions', 'statusOptions'
        ));
    }

    public function store(Request $request)
    {
        $profile = $this->getProfile();

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'price' => 'nullable|numeric|min:0',
            'dp_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'payment_deadline' => 'nullable|date',
        ]);

        $validated['wp_profile_id'] = $profile->id;
        $validated['remaining_amount'] = max(0, ($validated['price'] ?? 0) - ($validated['dp_amount'] ?? 0));

        WpVendor::create($validated);

        return back()->with('success', 'Vendor berhasil ditambahkan!');
    }

    public function update(Request $request, WpVendor $vendor)
    {
        $this->authorizeVendor($vendor);

        $validated = $request->validate([
            'category' => 'sometimes|string|max:50',
            'name' => 'sometimes|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'price' => 'nullable|numeric|min:0',
            'dp_amount' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:prospek,deal,dp_paid,lunas,cancelled',
            'notes' => 'nullable|string|max:1000',
            'payment_deadline' => 'nullable|date',
        ]);

        if (isset($validated['price']) || isset($validated['dp_amount'])) {
            $price = $validated['price'] ?? (float) $vendor->price;
            $dp = $validated['dp_amount'] ?? (float) $vendor->dp_amount;
            $validated['remaining_amount'] = max(0, $price - $dp);
        }

        $vendor->update($validated);

        return back()->with('success', 'Vendor diupdate!');
    }

    public function destroy(WpVendor $vendor)
    {
        $this->authorizeVendor($vendor);
        $vendor->delete();
        return back()->with('success', 'Vendor dihapus.');
    }

    public function markDpPaid(WpVendor $vendor)
    {
        $this->authorizeVendor($vendor);
        $vendor->update([
            'dp_paid_at' => now(),
            'status' => 'dp_paid',
        ]);
        return back()->with('success', 'DP ditandai lunas!');
    }

    public function markFullPaid(WpVendor $vendor)
    {
        $this->authorizeVendor($vendor);
        $vendor->update([
            'remaining_paid_at' => now(),
            'status' => 'lunas',
        ]);
        return back()->with('success', 'Pembayaran vendor lunas!');
    }

    private function getProfile(): WpProfile
    {
        $profile = WpProfile::where('user_id', auth()->id())->first();
        if (!$profile || !$profile->onboarding_completed) {
            abort(redirect()->route('client.planner.onboarding.step1'));
        }
        return $profile;
    }

    private function authorizeVendor(WpVendor $vendor): void
    {
        if ($vendor->profile->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
