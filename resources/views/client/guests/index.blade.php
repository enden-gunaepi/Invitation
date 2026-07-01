@extends('layouts.client')
@section('title', 'Kelola Tamu')
@section('page-title', 'Kelola Tamu')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-base">Daftar Tamu</h3>
                    <span class="text-xs font-semibold" style="color: var(--text-secondary);">{{ $currentGuests }}/{{ $maxGuests }}</span>
                </div>
                <div class="flex items-center gap-3 text-xs mb-2" style="color: var(--text-secondary);">
                    <span><i class="fas fa-check-circle mr-1"></i>Check-in: {{ $checkedInGuests ?? 0 }}</span>
                    <span><i class="fas fa-chair mr-1"></i>Seat assigned: {{ $seatAssignedGuests ?? 0 }}</span>
                </div>
                @php $percent = $maxGuests > 0 ? min(100, round(($currentGuests / $maxGuests) * 100)) : 0; @endphp
                <div style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                    <div style="width: {{ $percent }}%; height: 100%; border-radius: 2px; transition: width 0.3s;
                        background: {{ $percent >= 90 ? 'var(--danger)' : ($percent >= 70 ? 'var(--warning)' : 'var(--accent)') }};"></div>
                </div>
                @if($percent >= 90)
                <p class="text-xs mt-1" style="color: var(--danger);">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Kuota tamu hampir penuh! {{ $percent >= 100 ? 'Upgrade paket untuk menambah tamu.' : '' }}
                </p>
                @endif
                <div class="mt-4">
                    <form
                        method="GET"
                        action="{{ route('client.invitations.guests.index', $invitation) }}"
                        class="flex flex-col sm:flex-row gap-2"
                        data-live-search-form
                    >
                        <input
                            type="text"
                            name="q"
                            value="{{ $search ?? '' }}"
                            class="form-input flex-1"
                            placeholder="Cari nama, no. HP, email, kategori, atau kursi"
                            autocomplete="off"
                            data-live-search-input
                        >
                        <button type="submit" class="btn btn-primary text-sm">
                            <i class="fas fa-search mr-2"></i>Cari
                        </button>
                        @if(!empty($search))
                        <a href="{{ route('client.invitations.guests.index', $invitation) }}" class="btn btn-secondary text-sm text-center">
                            Reset
                        </a>
                        @endif
                    </form>
                    <p class="text-xs mt-2" style="color: var(--text-secondary);">
                        Urutan daftar menampilkan tamu terbaru di bagian paling atas.
                    </p>
                </div>
            </div>
            <div class="p-4" data-guest-list>
                @include('client.guests.partials.list', ['guests' => $guests, 'invitation' => $invitation, 'search' => $search])
            </div>
        </div>
        <div class="mt-4" data-guest-pagination>{{ $guests->links() }}</div>
    </div>

    <div>
        <div class="card p-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="stat-icon" style="background: var(--accent-bg); color: var(--accent); width:32px; height:32px; font-size:13px;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold" style="color: var(--text-secondary);">Paket {{ $invitation->package->name ?? '-' }}</p>
                    <p class="text-sm font-bold">{{ $currentGuests }} / {{ $maxGuests }} tamu</p>
                </div>
            </div>
            <a href="{{ route('client.invitations.checkin', $invitation) }}" class="btn btn-secondary w-full text-center block text-sm mt-3">
                <i class="fas fa-qrcode mr-2"></i> Scanner Check-in Hari H
            </a>
        </div>

        @if($currentGuests < $maxGuests)
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Tambah Tamu</h3>
            <form method="POST" action="{{ route('client.invitations.guests.store', $invitation) }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-input" required placeholder="Nama tamu">
                    @error('name') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">No. HP</label>
                    <input type="text" name="phone" class="form-input" placeholder="08xxx">
                </div>
                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="email@contoh.com">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Kategori</label>
                        <input type="text" name="category" class="form-input" placeholder="Keluarga">
                    </div>
                    <div>
                        <label class="form-label">Jumlah Kursi</label>
                        <input type="number" name="pax" class="form-input" value="1" min="1" max="10" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full text-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Tamu
                </button>
            </form>
        </div>

        <div class="card p-6 mt-4">
            <h3 class="font-bold text-base mb-2">Import Tamu dari Excel</h3>
            <p class="text-xs mb-4" style="color: var(--text-secondary);">
                Upload file <strong>.xlsx/.xls/.csv</strong>. Format yang didukung: data per kolom Excel dengan header <code>name</code>, <code>phone</code>, <code>email</code>, <code>category</code>, <code>pax</code>, <code>notes</code>. Template CSV download memakai pemisah yang cocok untuk Excel lokal agar kolom langsung terpisah. Kolom yang kosong tetap boleh diimpor.
            </p>
            <form method="POST" action="{{ route('client.invitations.guests.import', $invitation) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">File Excel / CSV</label>
                    <input type="file" name="guest_file" class="form-input" accept=".xlsx,.xls,.csv,.txt" required>
                    @error('guest_file') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full text-sm">
                    <i class="fas fa-file-import mr-2"></i> Import Tamu
                </button>
            </form>
            <a href="{{ asset('templates/guest-import-template.csv') }}" class="text-xs font-semibold inline-block mt-3" style="color: var(--accent);" download>
                <i class="fas fa-download mr-1"></i> Download template CSV
            </a>
        </div>

        <div class="card p-6 mt-4">
            <h3 class="font-bold text-base mb-2">Auto Seating Plan</h3>
            <p class="text-xs mb-4" style="color: var(--text-secondary);">Buat pembagian meja dan kursi otomatis untuk seluruh tamu.</p>
            <form method="POST" action="{{ route('client.invitations.guests.auto-seat', $invitation) }}">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="form-label">Kursi per Meja</label>
                        <input type="number" name="seats_per_table" class="form-input" value="8" min="2" max="20" required>
                    </div>
                    <div>
                        <label class="form-label">Meja Awal</label>
                        <input type="number" name="start_table" class="form-input" value="1" min="1" max="999">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full text-sm">
                    <i class="fas fa-chair mr-2"></i> Generate Seating Plan
                </button>
            </form>
        </div>
        @else
        <div class="card p-6 text-center">
            <i class="fas fa-lock text-2xl mb-3" style="color: var(--text-tertiary);"></i>
            <p class="text-sm font-semibold mb-1">Batas Tamu Tercapai</p>
            <p class="text-xs" style="color: var(--text-secondary);">Upgrade paket untuk menambah lebih banyak tamu.</p>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-live-search-form]');
    const input = document.querySelector('[data-live-search-input]');
    const listContainer = document.querySelector('[data-guest-list]');
    const paginationContainer = document.querySelector('[data-guest-pagination]');

    if (!form || !input || !listContainer || !paginationContainer) {
        return;
    }

    let timerId = null;
    let lastSubmittedValue = input.value.trim();
    let activeController = null;

    const setLoadingState = function (isLoading) {
        input.setAttribute('aria-busy', isLoading ? 'true' : 'false');
        listContainer.style.opacity = isLoading ? '0.65' : '1';
    };

    const applyResponse = function (payload, url) {
        listContainer.innerHTML = payload.list_html;
        paginationContainer.innerHTML = payload.pagination_html;
        window.history.replaceState({}, '', url);
    };

    const fetchResults = function (url) {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();
        setLoadingState(true);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            signal: activeController.signal,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Search request failed');
                }

                return response.json();
            })
            .then(function (payload) {
                applyResponse(payload, url);
            })
            .catch(function (error) {
                if (error.name !== 'AbortError') {
                    window.location.href = url;
                }
            })
            .finally(function () {
                setLoadingState(false);
            });
    };

    input.addEventListener('input', function () {
        window.clearTimeout(timerId);

        timerId = window.setTimeout(function () {
            const currentValue = input.value.trim();

            if (currentValue === lastSubmittedValue) {
                return;
            }

            lastSubmittedValue = currentValue;
            const url = new URL(form.action);

            if (currentValue !== '') {
                url.searchParams.set('q', currentValue);
            }

            fetchResults(url.toString());
        }, 250);
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const currentValue = input.value.trim();
        lastSubmittedValue = currentValue;

        const url = new URL(form.action);
        if (currentValue !== '') {
            url.searchParams.set('q', currentValue);
        }

        fetchResults(url.toString());
    });

    document.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('[data-guest-pagination] a');

        if (!paginationLink) {
            return;
        }

        event.preventDefault();
        fetchResults(paginationLink.href);
    });
});
</script>
@endpush
