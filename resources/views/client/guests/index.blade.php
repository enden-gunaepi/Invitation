@extends('layouts.client')
@section('title', 'Kelola Tamu')
@section('page-title', 'Kelola Tamu')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Guest List --}}
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-[rgba(16,185,129,0.1)] flex items-center justify-between">
                <h3 class="font-bold text-base">Daftar Tamu ({{ $guests->total() }})</h3>
            </div>
            <div class="p-4">
                @forelse($guests as $guest)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-[rgba(16,185,129,0.05)] transition mb-1">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center text-emerald-400 text-sm font-bold">
                        {{ substr($guest->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold">{{ $guest->name }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $guest->category ?? 'Umum' }} · {{ $guest->pax }} orang
                            @if($guest->phone) · {{ $guest->phone }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($invitation->isActive())
                        <button onclick="navigator.clipboard.writeText('{{ $guest->getInvitationUrl() }}'); this.innerHTML='<i class=\'fas fa-check text-emerald-400\'></i>'; setTimeout(() => this.innerHTML='<i class=\'fas fa-link text-slate-400\'></i>', 2000);"
                                class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-slate-700 transition" title="Copy link">
                            <i class="fas fa-link text-slate-400"></i>
                        </button>
                        @endif
                        <form method="POST" action="{{ route('client.invitations.guests.destroy', [$invitation, $guest]) }}" onsubmit="return confirm('Hapus tamu ini?')">
                            @csrf @method('DELETE')
                            <button class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-red-900/30 transition">
                                <i class="fas fa-trash text-red-400 text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-500">
                    <i class="fas fa-users text-3xl mb-3 opacity-40"></i>
                    <p class="text-sm">Belum ada tamu. Tambahkan dari form di samping.</p>
                </div>
                @endforelse
            </div>
        </div>
        <div class="mt-4">{{ $guests->links() }}</div>
    </div>

    {{-- Add Guest Form --}}
    <div>
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Tambah Tamu</h3>
            <form method="POST" action="{{ route('client.invitations.guests.store', $invitation) }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-input" required placeholder="Nama tamu">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
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
                <button type="submit" class="btn-primary w-full text-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Tamu
                </button>
            </form>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-emerald-400 text-sm hover:text-emerald-300">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection
