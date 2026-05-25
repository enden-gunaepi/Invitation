<?php

namespace App\Console\Commands;

use App\Services\InvitationMediaCleanupService;
use Illuminate\Console\Command;

class CleanupInvitationMedia extends Command
{
    protected $signature = 'invitations:cleanup-media {--dry-run : Tampilkan file orphan tanpa menghapus}';
    protected $description = 'Hapus file media undangan yang tidak lagi direferensikan database.';

    public function handle(InvitationMediaCleanupService $cleanupService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $inspection = $cleanupService->inspectOrphanMedia();

        if ($dryRun) {
            foreach ($inspection['files'] as $file) {
                $this->line('[dry-run] ' . $file['path']);
            }
            $this->info("Dry-run selesai. Kandidat orphan media: {$inspection['totals']['count']} file.");
            return self::SUCCESS;
        }

        $result = $cleanupService->cleanupOrphanMedia();
        foreach ($inspection['files'] as $file) {
            $this->line('[deleted] ' . $file['path']);
        }
        $this->info("Cleanup selesai. {$result['deleted']} file dihapus, total " . number_format($result['bytes'] / 1048576, 2) . ' MB dibebaskan.');
        return self::SUCCESS;
    }
}
