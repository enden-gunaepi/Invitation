<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            $brandAppName = Setting::get('app_name', config('app.name'));
            $brandName = Setting::get('company_name', $brandAppName);
            $brandLogoPath = Setting::get('company_logo');

            $view->with([
                'brandAppName' => $brandAppName,
                'brandName' => $brandName,
                'brandLogoPath' => $brandLogoPath,
                'brandLogoUrl' => $brandLogoPath ? Storage::url($brandLogoPath) : null,
                'brandPhone' => Setting::get('company_phone'),
                'brandEmail' => Setting::get('company_email'),
                'brandAddress' => Setting::get('company_address'),
                'brandInstagram' => Setting::get('company_instagram'),
                'brandFacebook' => Setting::get('company_facebook'),
                'brandDomain' => Setting::get('app_domain'),
            ]);
        });

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
