@extends('layouts.client')
@section('title', 'Pilih Paket')
@section('page-title', 'Pilih Paket Langganan')
@section('page-subtitle', 'Aktifkan paket dulu sebelum membuat undangan')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    @if($activeSubscription && $activeSubscription->package)
    <div class="card p-4" style="border-color: rgba(52,199,89,.35);">
        <p class="text-sm font-semibold" style="color: var(--success);">
            Paket aktif saat ini: {{ $activeSubscription->package->name }}
        </p>
        <p class="text-xs mt-1" style="color: var(--text-secondary);">
            Berlaku sampai {{ $activeSubscription->expires_at?->format('d M Y H:i') ?? 'tanpa batas waktu' }}.
        </p>
        <a href="{{ route('client.invitations.create') }}" class="btn btn-primary mt-3 inline-block text-sm">Buat Undangan</a>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($packages as $package)
        <div class="card p-5">
            <h3 class="font-bold text-base">{{ $package->name }}</h3>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">{{ $package->description }}</p>
            <p class="text-xl font-bold mt-4" style="color: var(--accent);">Rp{{ number_format((float) $package->price, 0, ',', '.') }}</p>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">
                {{ ($package->billing_type ?? 'one_time') === 'subscription' ? 'Subscription ' . strtoupper($package->billing_cycle ?? 'monthly') : 'One-time' }}
            </p>

            <div class="mt-4 text-xs space-y-1" style="color: var(--text-secondary);">
                <p>Max Undangan: <strong>{{ $package->max_invitations ?? 1 }}</strong></p>
                <p>Max Tamu/Undangan: <strong>{{ $package->max_guests ?? 100 }}</strong></p>
                <p>Max Foto/Undangan: <strong>{{ $package->max_photos ?? 10 }}</strong></p>
            </div>

            <form method="POST" action="{{ route('client.packages.select.store') }}" class="mt-4">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <button type="submit" class="btn btn-primary w-full text-sm">
                    Pilih Paket Ini
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection

