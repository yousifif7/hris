<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BackgroundCheckController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\JobCategoryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\PreScreeningController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TimeOffController;
use App\Http\Controllers\Api\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth (public)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public — candidate self-service endpoints
|--------------------------------------------------------------------------
*/
Route::prefix('public')->group(function () {
    // Candidate submits their own resume (public apply form)
    Route::post('/apply', [CandidateController::class, 'store']);

    // Job categories for the public apply form dropdown
    Route::get('/job-categories', [JobCategoryController::class, 'index']);

    // Candidate self-books an interview via scheduling link
    Route::post('/interviews/book', [InterviewController::class, 'publicBook']);

    // Reference responds to questionnaire
    Route::patch('/references/{reference}/respond', [ReferenceController::class, 'submitResponse']);

    // Candidate responds to offer
    Route::patch('/offers/{offer}/respond', [OfferController::class, 'respond']);
});

/*
|--------------------------------------------------------------------------
| Authenticated routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'hr'])->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Job categories
    Route::get('/job-categories', [JobCategoryController::class, 'index']);
    Route::post('/job-categories', [JobCategoryController::class, 'store']);
    Route::patch('/job-categories/{jobCategory}', [JobCategoryController::class, 'update']);
    Route::delete('/job-categories/{jobCategory}', [JobCategoryController::class, 'destroy']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Candidates
    Route::apiResource('candidates', CandidateController::class);
    Route::get('/candidates-review-queue', [CandidateController::class, 'reviewQueue']);
    Route::get('/candidates-pipeline', [CandidateController::class, 'pipeline']);
    Route::post('/candidates-upload', [CandidateController::class, 'uploadResume']);
    Route::patch('/candidates/{candidate}/status', [CandidateController::class, 'updateStatus']);
    Route::post('/candidates/{candidate}/advance', [CandidateController::class, 'advance']);
    Route::post('/candidates/{candidate}/convert', [CandidateController::class, 'convertToEmployee']);

    // Pre-screening
    Route::get('/candidates/{candidate}/prescreen', [PreScreeningController::class, 'show']);
    Route::post('/candidates/{candidate}/prescreen', [PreScreeningController::class, 'store']);

    // Interviews
    Route::apiResource('interviews', InterviewController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/interviews/{interview}/complete', [InterviewController::class, 'complete']);

    // Background checks
    Route::get('/candidates/{candidate}/background-checks', [BackgroundCheckController::class, 'index']);
    Route::patch('/background-checks/{backgroundCheck}', [BackgroundCheckController::class, 'update']);

    // References
    Route::get('/candidates/{candidate}/references', [ReferenceController::class, 'index']);
    Route::post('/candidates/{candidate}/references', [ReferenceController::class, 'store']);
    Route::delete('/candidates/{candidate}/references/{reference}', [ReferenceController::class, 'destroy']);

    // Offers
    Route::apiResource('offers', OfferController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/offers/{offer}/respond', [OfferController::class, 'respond']);

    // Onboarding
    Route::get('/onboarding', [OnboardingController::class, 'index']);
    Route::patch('/onboarding-tasks/{task}/toggle', [OnboardingController::class, 'completeTask']);
    Route::delete('/onboarding-tasks/{task}', [OnboardingController::class, 'destroyTask']);
    Route::get('/onboarding-templates', [OnboardingController::class, 'templates']);
    Route::post('/onboarding-templates', [OnboardingController::class, 'storeTemplate']);
    Route::patch('/onboarding-templates/{template}', [OnboardingController::class, 'updateTemplate']);
    Route::delete('/onboarding-templates/{template}', [OnboardingController::class, 'destroyTemplate']);

    // Employees
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Time Off
    Route::get('/time-off', [TimeOffController::class, 'index']);
    Route::post('/time-off', [TimeOffController::class, 'store']);
    Route::patch('/time-off/{timeOffRequest}', [TimeOffController::class, 'update']);
    Route::delete('/time-off/{timeOffRequest}', [TimeOffController::class, 'destroy']);
    Route::patch('/time-off/{timeOffRequest}/review', [TimeOffController::class, 'review']);

    // Trainings
    Route::get('/trainings', [TrainingController::class, 'index']);
    Route::post('/trainings', [TrainingController::class, 'store']);
    Route::patch('/trainings/{training}', [TrainingController::class, 'update']);
    Route::delete('/trainings/{training}', [TrainingController::class, 'destroy']);
    Route::patch('/trainings/{training}/complete', [TrainingController::class, 'complete']);

    // Documents
    Route::post('/documents', [DocumentController::class, 'upload']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::put('/settings', [SettingsController::class, 'update']);
    Route::get('/settings/apply-link', [SettingsController::class, 'applyLink']);
    Route::post('/settings/apply-link/regenerate', [SettingsController::class, 'regenerateApplyLink']);
    Route::get('/settings/automations', [SettingsController::class, 'automationRules']);
    Route::get('/settings/email-templates', [SettingsController::class, 'emailTemplates']);
    Route::get('/settings/email-templates/{template}', [SettingsController::class, 'showEmailTemplate']);
    Route::post('/settings/email-templates', [SettingsController::class, 'createEmailTemplate']);
    Route::put('/settings/email-templates/{template}', [SettingsController::class, 'updateEmailTemplate']);
    Route::delete('/settings/email-templates/{template}', [SettingsController::class, 'destroyEmailTemplate']);
    Route::get('/settings/email-tokens', [SettingsController::class, 'emailTokens']);
    Route::get('/settings/hr-team', [SettingsController::class, 'hrTeam']);

    // Candidate quick-send (email + SMS)
    Route::post('/candidates/{candidate}/send-email', [MessageController::class, 'sendToCandidate']);
    Route::post('/candidates/{candidate}/send-sms', [MessageController::class, 'sendSmsToCandidate']);
    Route::post('/candidates/bulk-email', [MessageController::class, 'bulkEmail']);
    Route::post('/candidates/bulk-sms', [MessageController::class, 'bulkSms']);

    // Notifications
    Route::get('/notifications', function () {
        return response()->json(auth()->user()->notifications()->latest()->limit(50)->get());
    });
    Route::post('/notifications/{id}/read', function (string $id) {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return response()->json(['ok' => true]);
    });
    Route::delete('/notifications/{id}', function (string $id) {
        auth()->user()->notifications()->where('id', $id)->delete();
        return response()->json(['ok' => true]);
    });
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    });
    Route::post('/notifications/bulk-read', function (Illuminate\Http\Request $request) {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'string'])['ids'];
        auth()->user()->notifications()->whereIn('id', $ids)->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    });
    Route::delete('/notifications/bulk-delete', function (Illuminate\Http\Request $request) {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'string'])['ids'];
        auth()->user()->notifications()->whereIn('id', $ids)->delete();
        return response()->json(['ok' => true]);
    });
    Route::delete('/notifications', function () {
        auth()->user()->notifications()->delete();
        return response()->json(['ok' => true]);
    });

    // Automations heartbeat — runs at most once per hour, triggered by the frontend poll
    Route::post('/automations/run', function () {
        $cacheKey = 'automations_last_run';
        $lastRun  = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if ($lastRun && now()->diffInMinutes($lastRun) < 60) {
            return response()->json(['ran' => false, 'next_in_minutes' => 60 - now()->diffInMinutes($lastRun)]);
        }

        \Illuminate\Support\Facades\Artisan::call('app:process-automations');

        \Illuminate\Support\Facades\Cache::put($cacheKey, now(), now()->addHours(2));

        return response()->json(['ran' => true, 'at' => now()->toTimeString()]);
    });
});

/*
|--------------------------------------------------------------------------
| Employee portal (role = employee, hr_staff, admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'employee'])->prefix('portal')->group(function () {

    // Own profile
    Route::get('/me', [AuthController::class, 'me']);

    // Own time-off requests
    Route::get('/time-off',  [TimeOffController::class, 'portalIndex']);
    Route::post('/time-off', [TimeOffController::class, 'portalStore']);

    // Own trainings
    Route::get('/trainings', [TrainingController::class, 'portalIndex']);

    // Own documents
    Route::get('/documents',  [DocumentController::class, 'portalIndex']);
    Route::post('/documents', [DocumentController::class, 'upload']);
});
