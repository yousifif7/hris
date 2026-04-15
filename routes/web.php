<?php

use App\Http\Controllers\PublicApplyController;
use Illuminate\Support\Facades\Route;

// Public career application form (token-masked URL)
Route::get('/apply/{token}', [PublicApplyController::class, 'show'])->name('public.apply');

// Root: redirect based on auth state (JS handles role-based redirect after /me)
Route::get('/', fn() => redirect('/login'));
Route::view('/login', 'auth.login')->name('login');

// Employee portal (employees land here after login)
Route::view('/portal', 'hris.portal')->name('portal');

Route::prefix('hris')->name('hris.')->group(function () {
    Route::view('/', 'hris.dashboard')->name('dashboard');
    Route::view('/intake', 'hris.intake')->name('intake');
    Route::view('/review', 'hris.review')->name('review');
    Route::view('/pipeline', 'hris.pipeline')->name('pipeline');
    Route::view('/interviews', 'hris.interviews')->name('interviews');
    Route::view('/screening', 'hris.screening')->name('screening');
    Route::view('/offers', 'hris.offers')->name('offers');
    Route::view('/onboarding', 'hris.onboarding')->name('onboarding');
    Route::view('/employee', 'hris.employee')->name('employees');
    Route::view('/timeoff', 'hris.timeoff')->name('timeoff');
    Route::view('/automations', 'hris.automations')->name('automations');
    Route::view('/settings', 'hris.settings')->name('settings');
});
