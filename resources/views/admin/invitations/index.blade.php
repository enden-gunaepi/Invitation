@extends('layouts.admin')

@section('title', 'Undangan')
@section('page-title', 'Kelola Undangan')
@section('page-subtitle', 'Review dan kelola semua undangan')

@section('content')
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <form class="flex items-center gap-3" method="GET">
            <div class="relative">
                {{-- <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i> --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari undangan..."
                    class="form-input pl-10 w-64">
            </div>
            <select name="status" class="form-input w-36" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </form>
    </div>

    <div class="card overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Undangan</th>
                    <th>Pemilik</th>
                    <th>Template</th>
                    <th>Tanggal</th>
                    <th>Kadaluarsa</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invitations as $inv)
                    <tr>
                        <td>
                            <div>
                                <p class="font-semibold text-sm">{{ Str::limit($inv->title, 30) }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst($inv->event_type) }}</p>
                            </div>
                        </td>
                        <td class="text-sm text-slate-400">{{ $inv->user->name }}</td>
                        <td class="text-sm text-slate-400">{{ $inv->template->name ?? '-' }}</td>
                        <td class="text-sm text-slate-400">{{ $inv->event_date->format('d M Y') }}</td>
                        <td class="text-sm">
                            @if(empty($inv->expires_at))
                                <div class="text-slate-400">Tanpa batas</div>
                            @elseif($inv->expires_at->isPast())
                                <div class="text-red-400 font-semibold">{{ $inv->expires_at->format('d M Y') }}</div>
                                <div class="text-xs text-red-300">Kadaluarsa {{ $inv->expires_at->diffForHumans() }}</div>
                            @else
                                <div class="text-emerald-400 font-semibold">{{ $inv->expires_at->format('d M Y') }}</div>
                                <div class="text-xs text-emerald-300">{{ $inv->expires_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span></td>
                        <td class="text-sm text-slate-400">{{ number_format($inv->view_count) }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.invitations.show', $inv) }}"
                                    class="text-indigo-400 hover:text-indigo-300 text-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ($inv->status === 'pending')
                                    <form method="POST" action="{{ route('admin.invitations.approve', $inv->id) }}"
                                        class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-emerald-400 hover:text-emerald-300 text-sm" title="Approve"><i
                                                class="fas fa-check"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.invitations.reject', $inv->id) }}"
                                        class="inline"
                                        onsubmit="event.preventDefault(); let notes = prompt('Alasan penolakan:'); if(notes) { this.querySelector('[name=admin_notes]').value = notes; this.submit(); }">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="admin_notes" value="">
                                        <button class="text-red-400 hover:text-red-300 text-sm" title="Reject"><i
                                                class="fas fa-times"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-slate-500">Tidak ada undangan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $invitations->links() }}</div>
@endsection
