<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\CandidateService::class);
    }

    public function boot(): void
    {
        // Load application timezone from settings so all date rendering/conversions
        // consistently follow the system timezone selected in Settings.
        try {
            if (Schema::hasTable('settings')) {
                $tz = Setting::get('timezone', config('app.timezone', 'UTC'));

                if (is_string($tz) && $tz !== '') {
                    config(['app.timezone' => $tz]);
                    date_default_timezone_set($tz);
                }
            }
        } catch (\Throwable $e) {
            // Ignore bootstrap-time DB issues (e.g., before migrations run).
        }
    }
}
