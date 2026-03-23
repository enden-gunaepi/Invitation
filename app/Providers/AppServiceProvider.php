<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('rsvp-submission', function (Request $request) {
            $slug = (string) $request->route('slug');
            return Limit::perMinute(8)->by($slug . '|' . $request->ip());
        });

        RateLimiter::for('wish-submission', function (Request $request) {
            $slug = (string) $request->route('slug');
            return Limit::perMinute(12)->by($slug . '|' . $request->ip());
        });
    }
}
