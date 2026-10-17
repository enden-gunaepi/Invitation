<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\SystemUpdateService;
use Illuminate\Console\Command;
use Throwable;

class UpdateSystemFromGit extends Command
{
    protected $signature = 'system:update {--check : Hanya fetch dan periksa perubahan tanpa pull}';
    protected $description = 'Periksa dan tarik update aplikasi dari repository Git yang dikonfigurasi';

    public function handle(SystemUpdateService $systemUpdate): int
    {
        $checkOnly = (bool) $this->option('check');

        if (!$checkOnly && Setting::get('system_auto_update', '0') !== '1') {
            $this->line('Auto update tidak aktif.');
            return self::SUCCESS;
        }

        try {
            if ($checkOnly) {
                $status = $systemUpdate->status(true);
                Setting::set('system_update_last_checked_at', now()->toDateTimeString(), 'system');
                $this->info("Pengecekan selesai. Behind={$status['behind']}, ahead={$status['ahead']}.");
                return self::SUCCESS;
            }

            $result = $systemUpdate->update();
            $message = $result['updated']
                ? "Auto update berhasil: {$result['before']['current_commit']} -> {$result['after']['current_commit']}."
                : 'Repository sudah terbaru.';

            Setting::set('system_update_last_checked_at', now()->toDateTimeString(), 'system');
            Setting::set('system_update_last_message', $message, 'system');
            if ($result['updated']) {
                Setting::set('system_update_last_updated_at', now()->toDateTimeString(), 'system');
            }
            $this->info($message);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            Setting::set('system_update_last_message', $exception->getMessage(), 'system');
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }
}
