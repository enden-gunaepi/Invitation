<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PurgeExpiredInvitations extends Command
{
    protected $signature = 'invitations:purge-expired {--dry-run : Hanya tampilkan jumlah tanpa menghapus}';
    protected $description = 'Tandai expired dan hapus undangan yang melewati expires_at beserta aset medianya.';

    public function handle(): int
    {
        $now = Carbon::now();
        $query = Invitation::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now);

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('Tidak ada undangan kadaluarsa untuk diproses.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry-run: {$total} undangan kadaluarsa terdeteksi.");
            return self::SUCCESS;
        }

        $processed = 0;
        $query->orderBy('id')->chunkById(100, function ($invitations) use (&$processed) {
            foreach ($invitations as $invitation) {
                try {
                    if ($invitation->status !== 'expired') {
                        $invitation->status = 'expired';
                        $invitation->save();
                    }

                    $invitation->delete();
                    $processed++;
                } catch (\Throwable $e) {
                    report($e);
                    $this->warn("Gagal memproses invitation ID {$invitation->id}");
                }
            }
        });

        $this->info("Selesai. {$processed} undangan kadaluarsa telah dihapus.");
        return self::SUCCESS;
    }
}
