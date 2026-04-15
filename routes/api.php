<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BackgroundCheckController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\JobCategoryController;
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
    Route::apiResource('interviews', InterviewController::class)->only(['index', 'store']);
    Route::patch('/interviews/{interview}/complete', [InterviewController::class, 'complete']);

    // Background checks
    Route::get('/candidates/{candidate}/background-checks', [BackgroundCheckController::class, 'index']);
    Route::patch('/background-checks/{backgroundCheck}', [BackgroundCheckController::class, 'update']);

    // References
    Route::get('/candidates/{candidate}/references', [ReferenceController::class, 'index']);
    Route::post('/candidates/{candidate}/references', [ReferenceController::class, 'store']);

    // Offers
    Route::apiResource('offers', OfferController::class)->only(['index', 'store']);
    Route::patch('/offers/{offer}/respond', [OfferController::class, 'respond']);

    // Onboarding
    Route::get('/onboarding', [OnboardingController::class, 'index']);
    Route::patch('/onboarding-tasks/{task}/toggle', [OnboardingController::class, 'completeTask']);
    Route::get('/onboarding-templates', [OnboardingController::class, 'templates']);
    Route::post('/onboarding-templates', [OnboardingController::class, 'storeTemplate']);

    // Employees
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Time Off
    Route::apiResource('time-off', TimeOffController::class)->only(['index', 'store']);
    Route::patch('/time-off/{timeOffRequest}/review', [TimeOffController::class, 'review']);

    // Trainings
    Route::get('/trainings', [TrainingController::class, 'index']);
    Route::post('/trainings', [TrainingController::class, 'store']);
    Route::patch('/trainings/{training}/complete', [TrainingController::class, 'complete']);

    // Documents
    Route::post('/documents', [DocumentController::class, 'upload']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::put('/settings', [SettingsController::class, 'update']);
    Route::get('/settings/apply-link', [SettingsController::class, 'applyLink']);
    Route::post('/settings/apply-link/regenerate', [SettingsController::class, 'regenerateApplyLink']);
    Route::get('/settings/automations', [SettingsController::class, 'automationRules']);
    Route::get('/settings/email-templates', [SettingsController::class, 'emailTemplates']);
    Route::put('/settings/email-templates/{template}', [SettingsController::class, 'updateEmailTemplate']);
    Route::get('/settings/hr-team', [SettingsController::class, 'hrTeam']);

    // Notifications
    Route::get('/notifications', function () {
        return response()->json(auth()->user()->notifications()->latest()->limit(30)->get());
    });
    Route::post('/notifications/{id}/read', function (string $id) {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return response()->json(['ok' => true]);
    });
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
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
