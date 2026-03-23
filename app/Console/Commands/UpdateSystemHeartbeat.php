<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class UpdateSystemHeartbeat extends Command
{
    protected $signature = 'system:heartbeat';
    protected $description = 'Update scheduler heartbeat timestamp for reliability monitoring';

    public function handle(): int
    {
        Setting::set('system_scheduler_heartbeat', now()->toDateTimeString(), 'system');
        $this->info('System heartbeat updated.');

        return self::SUCCESS;
    }
}
