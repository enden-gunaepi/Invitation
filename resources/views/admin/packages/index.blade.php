@extends('layouts.admin')
@section('title', 'Kelola Paket')
@section('page-title', 'Kelola Paket')
@section('page-subtitle', 'Manajemen paket harga')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-400">{{ $packages->total() }} paket</p>
        <a href="{{ route('admin.packages.create') }}" class="btn-primary text-sm px-2 py-1 rounded-md"><i
                class="fas fa-plus mr-2"></i> Tambah Paket</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($packages as $pkg)
            <div class="card p-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-cyan-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-lg">{{ $pkg->name }}</h4>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">Tier
                            {{ ucfirst($pkg->tier ?? 'starter') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="badge {{ $pkg->is_active ? 'badge-active' : 'badge-draft' }}">
                            {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        @if ($pkg->is_recommended)
                            <div class="text-[10px] mt-1 px-2 py-1 rounded-full inline-block"
                                style="background: var(--accent-bg); color: var(--accent);">Recommended</div>
                        @endif
                    </div>
                </div>
                <p class="text-3xl font-bold mb-1">
                    Rp{{ number_format($pkg->price, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500 mb-4">per undangan</p>
                @if ($pkg->badge_text)
                    <p class="text-xs font-semibold mb-3" style="color: var(--accent);">{{ $pkg->badge_text }}</p>
                @endif
                <div class="space-y-2 mb-5">
                    <div class="text-sm text-slate-400">
                        <i class="fas fa-users text-indigo-400 mr-2 w-4"></i> Max {{ $pkg->max_guests }} tamu
                    </div>
                    <div class="text-sm text-slate-400">
                        <i class="fas fa-image text-indigo-400 mr-2 w-4"></i> Max {{ $pkg->max_photos }} foto
                    </div>
                    <div class="text-sm text-slate-400">
                        <i class="fas fa-layer-group text-indigo-400 mr-2 w-4"></i> Max {{ $pkg->max_invitations }}
                        undangan
                    </div>
                    <div class="text-sm text-slate-400">
                        <i class="fas fa-money-check-dollar text-indigo-400 mr-2 w-4"></i>
                        {{ ($pkg->billing_type ?? 'one_time') === 'subscription' ? 'Subscription ' . strtoupper($pkg->billing_cycle ?? 'monthly') : 'One-time payment' }}
                    </div>
                    <div class="text-sm text-slate-400">
                        <i class="fas fa-hourglass-half text-indigo-400 mr-2 w-4"></i>
                        @if(!empty($pkg->active_duration_value) && !empty($pkg->active_duration_unit))
                            Aktif {{ $pkg->active_duration_value }} {{ $pkg->active_duration_unit === 'month' ? 'bulan' : 'hari' }}
                        @else
                            Aktif tanpa batas
                        @endif
                    </div>
                    <div class="text-sm text-slate-400">
                        <i class="fas fa-percent text-indigo-400 mr-2 w-4"></i>
                        Komisi affiliate: {{ rtrim(rtrim(number_format((float) ($pkg->affiliate_commission_rate ?? 5), 2, '.', ''), '0'), '.') }}%
                    </div>
                    @if ($pkg->support_level)
                        <div class="text-sm text-slate-400">
                            <i class="fas fa-headset text-indigo-400 mr-2 w-4"></i> {{ $pkg->support_level }}
                            @if ($pkg->sla_hours)
                                (SLA {{ $pkg->sla_hours }} jam)
                            @endif
                        </div>
                    @endif
                    @if ($pkg->features)
                        @foreach ($pkg->features as $feature)
                            <div class="text-sm text-slate-400">
                                <i class="fas fa-check text-emerald-400 mr-2 w-4"></i> {{ $feature }}
                            </div>
                        @endforeach
                    @endif
                    @if ($pkg->addons)
                        @foreach ($pkg->addons as $addon)
                            <div class="text-sm text-slate-400">
                                <i class="fas fa-star text-amber-400 mr-2 w-4"></i> {{ $addon }}
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.packages.edit', $pkg) }}"
                        class="btn-outline text-xs py-2 px-4 flex-1 text-center">Edit</a>
                    <form method="POST" action="{{ route('admin.packages.destroy', $pkg) }}"
                        onsubmit="return confirm('Hapus paket?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 text-sm px-3 py-2"><i
                                class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-slate-500">
                <i class="fas fa-box text-4xl mb-4 opacity-40"></i>
                <p>Belum ada paket</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $packages->links() }}</div>
@endsection
