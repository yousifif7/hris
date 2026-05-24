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
        $middleware->alias([
            'hr'        => \App\Http\Middleware\EnsureHrStaff::class,
            'employee'  => \App\Http\Middleware\EnsureEmployee::class,
            'candidate' => \App\Http\Middleware\EnsureCandidate::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Daily automations: no-response follow-ups, offer expiry, expiring trainings
        $schedule->command('app:process-automations')
            ->dailyAt('08:00')
            ->name('process-automations')
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
