@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen pengguna sistem')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form class="flex items-center gap-3" method="GET">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user..." class="form-input pl-10 w-64">
        </div>
        <select name="role" class="form-input w-36" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Client</option>
        </select>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn-primary text-sm">
        <i class="fas fa-plus mr-2"></i> Tambah User
    </a>
</div>

<div class="card overflow-hidden">
    <table class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Bergabung</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <span class="font-semibold text-sm">{{ $user->name }}</span>
                    </div>
                </td>
                <td class="text-slate-400 text-sm">{{ $user->email }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-indigo-500/15 text-indigo-400' : 'bg-emerald-500/15 text-emerald-400' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>
                    @if($user->is_active)
                        <span class="badge badge-active"><i class="fas fa-circle text-[8px] mr-1"></i> Aktif</span>
                    @else
                        <span class="badge badge-rejected"><i class="fas fa-circle text-[8px] mr-1"></i> Nonaktif</span>
                    @endif
                </td>
                <td class="text-slate-400 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                <td class="text-right">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-400 hover:text-indigo-300 text-sm mr-3">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Hapus user ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 text-sm"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-8 text-slate-500">Tidak ada data user</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
