<?php

return [
    'enabled' => env('SYSTEM_UPDATE_ENABLED', true),
    'path' => env('SYSTEM_UPDATE_PATH', base_path()),
    'remote' => env('SYSTEM_UPDATE_REMOTE', 'origin'),
    'branch' => env('SYSTEM_UPDATE_BRANCH', 'main'),
    'timeout' => (int) env('SYSTEM_UPDATE_TIMEOUT', 180),
    'schedule_timezone' => env('SCHEDULE_TIMEZONE', 'Asia/Jakarta'),
];
