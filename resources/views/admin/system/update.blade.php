@extends('layouts.admin')
@section('title', 'System Update')
@section('page-title', 'System Update')
@section('page-subtitle', 'Kelola versi aplikasi dan pembaruan dari GitHub')

@section('content')
    @if ($repositoryError)
        <div class="card mb-6" style="border-color: rgba(186,26,26,.35);">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined" style="color: var(--danger);">error</span>
                <div>
                    <h3 class="font-semibold">Repository tidak dapat dibaca</h3>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $repositoryError }}</p>
                </div>
            </div>
        </div>
    @else
        @php
            $updateAvailable = $repository['behind'] > 0;
            $canPull = $repository['enabled'] && !$repository['dirty'] && $repository['branch'] === $repository['configured_branch'];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div class="card">
                <div class="text-xs uppercase tracking-wide" style="color: var(--text-secondary);">Status</div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="material-symbols-outlined" style="color: {{ $updateAvailable ? 'var(--warning)' : 'var(--success)' }};">
                        {{ $updateAvailable ? 'system_update_alt' : 'verified' }}
                    </span>
                    <span class="font-semibold">{{ $updateAvailable ? $repository['behind'].' update tersedia' : 'Sudah terbaru' }}</span>
                </div>
            </div>
            <div class="card">
                <div class="text-xs uppercase tracking-wide" style="color: var(--text-secondary);">Branch aktif</div>
                <div class="font-semibold mt-2 font-mono">{{ $repository['branch'] ?: '(detached HEAD)' }}</div>
                @if ($repository['branch'] !== $repository['configured_branch'])
                    <div class="text-xs mt-1" style="color: var(--danger);">Harus {{ $repository['configured_branch'] }}</div>
                @endif
            </div>
            <div class="card">
                <div class="text-xs uppercase tracking-wide" style="color: var(--text-secondary);">Versi terpasang</div>
                <div class="font-semibold mt-2 font-mono">{{ $repository['current_commit'] }}</div>
                <div class="text-xs mt-1" style="color: var(--text-secondary);">Ahead {{ $repository['ahead'] }} · Behind {{ $repository['behind'] }}</div>
            </div>
            <div class="card">
                <div class="text-xs uppercase tracking-wide" style="color: var(--text-secondary);">Working tree</div>
                <div class="font-semibold mt-2" style="color: {{ $repository['dirty'] ? 'var(--danger)' : 'var(--success)' }};">
                    {{ $repository['dirty'] ? 'Ada perubahan lokal' : 'Bersih' }}
                </div>
                <div class="text-xs mt-1" style="color: var(--text-secondary);">File untracked tidak menghalangi update</div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
            <div class="card xl:col-span-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-base">Repository GitHub</h3>
                        <p class="text-sm mt-1 break-all" style="color: var(--text-secondary);">
                            {{ $repository['remote'] }} · {{ $repository['remote_url'] ?: 'Remote URL tidak tersedia' }}
                        </p>
                        @if ($lastCheckedAt)
                            <p class="text-xs mt-2" style="color: var(--text-tertiary);">Terakhir dicek: {{ \Carbon\Carbon::parse($lastCheckedAt)->format('d M Y H:i:s') }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.system.update.check') }}">
                            @csrf
                            <button class="btn btn-secondary" type="submit">
                                <span class="material-symbols-outlined" style="font-size:18px;">sync</span>
                                Cek Update
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.system.update.pull') }}" onsubmit="return confirm('Tarik update terbaru dari GitHub sekarang?')">
                            @csrf
                            <button class="btn btn-primary" type="submit" {{ !$canPull ? 'disabled' : '' }} style="{{ !$canPull ? 'opacity:.5;cursor:not-allowed;' : '' }}">
                                <span class="material-symbols-outlined" style="font-size:18px;">download</span>
                                Pull Update
                            </button>
                        </form>
                    </div>
                </div>
                @if (!$repository['enabled'])
                    <p class="text-sm mt-4" style="color: var(--danger);">Update dari panel dinonaktifkan oleh SYSTEM_UPDATE_ENABLED.</p>
                @elseif ($repository['dirty'])
                    <p class="text-sm mt-4" style="color: var(--danger);">Commit atau simpan perubahan tracked lokal sebelum melakukan pull.</p>
                @endif
                @if ($lastUpdateMessage)
                    <div class="mt-4 p-3 rounded-lg text-sm" style="background: var(--surface-container-low); color: var(--text-secondary);">
                        {{ $lastUpdateMessage }}
                        @if ($lastUpdatedAt)
                            <span class="block text-xs mt-1">Update terakhir: {{ \Carbon\Carbon::parse($lastUpdatedAt)->format('d M Y H:i:s') }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold">Update Otomatis</h3>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">Cek dan pull fast-forward setiap 5 menit.</p>
                    </div>
                    <span class="badge {{ $autoUpdate ? 'badge-success' : 'badge-default' }}">{{ $autoUpdate ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <form method="POST" action="{{ route('admin.system.update.settings') }}" class="mt-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="auto_update" value="0">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="auto_update" value="1" class="mt-1" {{ $autoUpdate ? 'checked' : '' }}>
                        <span class="text-sm">Aktifkan pull otomatis ketika commit baru terdeteksi.</span>
                    </label>
                    <button type="submit" class="btn btn-secondary mt-4 w-full justify-center">Simpan Pengaturan</button>
                </form>
                <div class="flex items-center gap-2 mt-4 text-xs" style="color: {{ $schedulerHealthy ? 'var(--success)' : 'var(--danger)' }};">
                    <span class="material-symbols-outlined" style="font-size:16px;">{{ $schedulerHealthy ? 'check_circle' : 'warning' }}</span>
                    <span>
                        Scheduler {{ $schedulerHealthy ? 'aktif' : 'belum terdeteksi' }}
                        @if ($schedulerHeartbeatAt)
                            · {{ $schedulerHeartbeatAt->format('d M H:i') }}
                        @endif
                    </span>
                </div>
                <p class="text-xs mt-3" style="color: var(--text-tertiary);">Membutuhkan cron server yang menjalankan <code>php artisan schedule:run</code> setiap menit.</p>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between gap-3" style="border-color: var(--border);">
                <div>
                    <h3 class="font-semibold text-base">15 Commit Terbaru</h3>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">{{ $repository['has_remote_ref'] ? $repository['remote'].'/'.$repository['configured_branch'] : 'Repository lokal' }}</p>
                </div>
            </div>
            <div class="divide-y" style="border-color: var(--border);">
                @forelse ($repository['commits'] as $commit)
                    <div class="px-6 py-4 flex items-start gap-4">
                        <code class="text-xs shrink-0 px-2 py-1 rounded" style="background: var(--surface-container); color: var(--accent);">{{ $commit['short_hash'] }}</code>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium break-words">{{ $commit['subject'] }}</p>
                            <p class="text-xs mt-1" style="color: var(--text-secondary);">
                                {{ $commit['author'] }} · {{ \Carbon\Carbon::parse($commit['date'])->format('d M Y H:i') }}
                            </p>
                        </div>
                        @if (str_starts_with($commit['hash'], $repository['current_commit']))
                            <span class="badge badge-info shrink-0">Terpasang</span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-center py-8" style="color: var(--text-secondary);">Belum ada riwayat commit.</p>
                @endforelse
            </div>
        </div>
    @endif
@endsection
