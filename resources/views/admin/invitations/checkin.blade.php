@extends('layouts.admin')
@section('title', 'Admin Check-in')
@section('page-title', 'Check-in Operasional')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-base">Scanner / Token Check-in</h3>
                <span class="badge badge-info">{{ $checkedIn }}/{{ $total }} hadir</span>
            </div>
            <p class="text-xs mb-4" style="color: var(--text-secondary);">Scan URL tamu atau masukkan token tamu untuk proses check-in.</p>
            <form method="POST" action="{{ route('admin.invitations.checkin.scan', $invitation) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label">Token / URL Undangan Tamu</label>
                    <input type="text" name="token" class="form-input" placeholder="Tempel URL /inv/{slug}/{token} atau token tamu" required>
                </div>
                <button class="btn btn-primary"><i class="fas fa-check-circle mr-1"></i> Proses Check-in</button>
            </form>
        </div>

        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Riwayat Check-in Terbaru</h3>
            <div class="space-y-2">
                @forelse($recentCheckins as $guest)
                    <div class="flex items-center justify-between text-sm p-2 rounded" style="background: var(--bg-tertiary);">
                        <div>
                            <strong>{{ $guest->name }}</strong>
                            <span style="color: var(--text-secondary);">- {{ $guest->category ?? 'Umum' }}</span>
                        </div>
                        <div style="color: var(--success); font-weight: 600;">{{ $guest->checked_in_at?->format('d/m H:i') }}</div>
                    </div>
                @empty
                    <p class="text-sm" style="color: var(--text-secondary);">Belum ada tamu check-in.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-6">
            <h3 class="font-bold text-base mb-3">Ringkasan</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span style="color: var(--text-secondary);">Undangan</span><strong>{{ $invitation->title }}</strong></div>
                <div class="flex justify-between"><span style="color: var(--text-secondary);">Client</span><strong>{{ $invitation->user->name ?? '-' }}</strong></div>
                <div class="flex justify-between"><span style="color: var(--text-secondary);">Tamu Total</span><strong>{{ $total }}</strong></div>
                <div class="flex justify-between"><span style="color: var(--text-secondary);">Sudah Check-in</span><strong style="color: var(--success);">{{ $checkedIn }}</strong></div>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.invitations.show', $invitation) }}" class="text-sm font-semibold" style="color: var(--accent);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke detail undangan
            </a>
        </div>
    </div>
</div>
@endsection

