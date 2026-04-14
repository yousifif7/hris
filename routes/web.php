<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/hris'));
Route::view('/login', 'auth.login')->name('login');

Route::prefix('hris')->name('hris.')->group(function () {
    Route::view('/', 'hris.dashboard')->name('dashboard');
    Route::view('/intake', 'hris.intake')->name('intake');
    Route::view('/review', 'hris.review')->name('review');
    Route::view('/pipeline', 'hris.pipeline')->name('pipeline');
    Route::view('/interviews', 'hris.interviews')->name('interviews');
    Route::view('/screening', 'hris.screening')->name('screening');
    Route::view('/offers', 'hris.offers')->name('offers');
    Route::view('/onboarding', 'hris.onboarding')->name('onboarding');
    Route::view('/employees', 'hris.employees')->name('employees');
    Route::view('/portal', 'hris.portal')->name('portal');
    Route::view('/timeoff', 'hris.timeoff')->name('timeoff');
    Route::view('/automations', 'hris.automations')->name('automations');
    Route::view('/settings', 'hris.settings')->name('settings');
});
