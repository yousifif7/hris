<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->alias([
            'hr'       => \App\Http\Middleware\EnsureHrStaff::class,
            'employee' => \App\Http\Middleware\EnsureEmployee::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // 7AM — expire overdue offers
        $schedule->call(function () {
            \App\Models\Offer::where('status', 'sent')->get()->each(function ($o) {
                if ($o->isExpired()) {
                    $o->update(['status' => 'expired']);
                }
            });
        })->dailyAt('07:00')->name('expire-offers');

        // 8AM — check expiring trainings
        $schedule->job(new \App\Jobs\CheckExpiringTrainings)
            ->dailyAt('08:00')
            ->name('check-expiring-trainings');

        // 9AM — no-response follow-ups
        $schedule->call(function () {
            $days = (int) \App\Models\Setting::get('followup_days', 5);
            \App\Models\Candidate::where('status', 'invite_sent')
                ->where('invite_sent_at', '<=', now()->subDays($days))
                ->each(fn ($c) => \App\Jobs\ProcessNoResponseFollowup::dispatch($c));
        })->dailyAt('09:00')->name('no-response-followups');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
