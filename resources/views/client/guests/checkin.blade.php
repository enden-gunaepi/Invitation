@extends('layouts.client')
@section('title', 'Scanner Check-in')
@section('page-title', 'Scanner Check-in')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="card p-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-base">Check-in Hari H</h3>
            <span class="text-xs font-semibold" style="color: var(--text-secondary);">{{ $checkedIn }}/{{ $total }} hadir</span>
        </div>
        <p class="text-xs mb-4" style="color: var(--text-secondary);">
            Scan token tamu dari QR / link undangan atau input manual token tamu.
        </p>
        <form method="POST" action="{{ route('client.invitations.checkin.scan', $invitation) }}" class="space-y-3">
            @csrf
            <div>
                <label class="form-label">Token / URL Undangan Tamu</label>
                <input type="text" name="token" class="form-input" placeholder="Tempel URL /inv/{slug}/{token} atau token saja" required>
            </div>
            <button class="btn btn-primary w-full text-sm">
                <i class="fas fa-check-circle mr-2"></i> Proses Check-in
            </button>
        </form>
    </div>

    <div class="card p-6">
        <h3 class="font-bold text-base mb-3">Tips Operasional</h3>
        <ul class="text-sm space-y-2" style="color: var(--text-secondary);">
            <li>1. Gunakan 1 petugas khusus check-in untuk menghindari duplikasi scan.</li>
            <li>2. Jalankan auto seating plan sebelum hari H untuk percepat pengaturan tamu.</li>
            <li>3. Jika tamu sudah check-in, sistem akan menampilkan waktu check-in sebelumnya.</li>
        </ul>
    </div>

    <div>
        <a href="{{ route('client.invitations.guests.index', $invitation) }}" class="text-sm font-semibold" style="color: var(--accent);">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Kelola Tamu
        </a>
    </div>
</div>
@endsection

