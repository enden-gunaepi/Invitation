<?php

namespace App\Services;

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class SystemUpdateService
{
    public function status(bool $fetch = false): array
    {
        $this->ensureRepository();

        if ($fetch) {
            $this->fetch();
        }

        $branch = trim($this->git(['branch', '--show-current'])->output());
        $remote = (string) config('system_update.remote', 'origin');
        $configuredBranch = (string) config('system_update.branch', 'main');
        $remoteRef = "{$remote}/{$configuredBranch}";
        $hasRemoteRef = $this->git(['show-ref', '--verify', '--quiet', "refs/remotes/{$remoteRef}"], false)->successful();
        $ahead = 0;
        $behind = 0;

        if ($hasRemoteRef) {
            $counts = preg_split('/\s+/', trim($this->git(['rev-list', '--left-right', '--count', "HEAD...{$remoteRef}"])->output()));
            $ahead = (int) ($counts[0] ?? 0);
            $behind = (int) ($counts[1] ?? 0);
        }

        $target = $hasRemoteRef ? $remoteRef : 'HEAD';
        $remoteUrl = trim($this->git(['remote', 'get-url', $remote], false)->output());

        return [
            'enabled' => (bool) config('system_update.enabled', true),
            'branch' => $branch,
            'configured_branch' => $configuredBranch,
            'remote' => $remote,
            'remote_url' => $this->redactRemoteUrl($remoteUrl),
            'current_commit' => trim($this->git(['rev-parse', '--short=12', 'HEAD'])->output()),
            'dirty' => trim($this->git(['status', '--porcelain', '--untracked-files=no'])->output()) !== '',
            'ahead' => $ahead,
            'behind' => $behind,
            'has_remote_ref' => $hasRemoteRef,
            'commits' => $this->commits($target, 15),
        ];
    }

    public function update(): array
    {
        if (!config('system_update.enabled', true)) {
            throw new RuntimeException('System Update dinonaktifkan melalui konfigurasi server.');
        }

        $lockPath = storage_path('framework/system-update.lock');
        $lock = fopen($lockPath, 'c+');

        if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) {
                fclose($lock);
            }
            throw new RuntimeException('Proses update lain sedang berjalan.');
        }

        try {
            $before = $this->status(false);

            if ($before['branch'] !== $before['configured_branch']) {
                throw new RuntimeException("Branch aktif harus {$before['configured_branch']}, saat ini {$before['branch']}.");
            }

            if ($before['dirty']) {
                throw new RuntimeException('Ada perubahan tracked yang belum di-commit. Update dibatalkan agar perubahan lokal aman.');
            }

            $this->fetch();
            $available = $this->status(false);

            if ($available['ahead'] > 0 && $available['behind'] > 0) {
                throw new RuntimeException('Branch lokal dan remote sudah divergen. Selesaikan konflik melalui terminal.');
            }

            if ($available['behind'] === 0) {
                return ['updated' => false, 'before' => $before, 'after' => $available, 'output' => 'Repository sudah terbaru.'];
            }

            $result = $this->git([
                'pull', '--ff-only',
                $available['remote'],
                $available['configured_branch'],
            ]);

            return [
                'updated' => true,
                'before' => $before,
                'after' => $this->status(false),
                'output' => trim($result->output()),
            ];
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function fetch(): void
    {
        $this->git([
            'fetch', '--prune',
            (string) config('system_update.remote', 'origin'),
            (string) config('system_update.branch', 'main'),
        ]);
    }

    private function commits(string $target, int $limit): array
    {
        $format = '%H%x1f%h%x1f%an%x1f%aI%x1f%s%x1e';
        $output = $this->git(['log', "-{$limit}", "--pretty=format:{$format}", $target])->output();
        $commits = [];

        foreach (explode("\x1e", $output) as $record) {
            $fields = explode("\x1f", trim($record));
            if (count($fields) !== 5) {
                continue;
            }

            $commits[] = [
                'hash' => $fields[0],
                'short_hash' => $fields[1],
                'author' => $fields[2],
                'date' => $fields[3],
                'subject' => $fields[4],
            ];
        }

        return $commits;
    }

    private function ensureRepository(): void
    {
        if (!is_dir($this->path().'/.git')) {
            throw new RuntimeException('Direktori aplikasi bukan repository Git.');
        }
    }

    private function git(array $arguments, bool $throw = true): ProcessResult
    {
        $result = Process::path($this->path())
            ->env(['GIT_TERMINAL_PROMPT' => '0'])
            ->timeout((int) config('system_update.timeout', 180))
            ->run(array_merge(['git'], $arguments));

        if ($throw && $result->failed()) {
            $message = trim($result->errorOutput()) ?: trim($result->output());
            throw new RuntimeException($message ? $this->redactRemoteUrl($message) : 'Perintah Git gagal dijalankan.');
        }

        return $result;
    }

    private function path(): string
    {
        return rtrim((string) config('system_update.path', base_path()), DIRECTORY_SEPARATOR);
    }

    private function redactRemoteUrl(string $url): string
    {
        return preg_replace('#(https?://)[^/@\s]+@#i', '$1***@', $url) ?? $url;
    }
}
