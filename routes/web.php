<?php

use App\Http\Controllers\PublicApplyController;
use App\Http\Controllers\HrisWorkflowController;
use App\Http\Controllers\PublicOfferController;
use App\Http\Controllers\PublicPreScreeningController;
use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\PrescreenPrintController;
use Illuminate\Support\Facades\Route;

// Public career application form (token-masked URL)
Route::get('/apply/{token}', [PublicApplyController::class, 'show'])->name('public.apply');

// Public offer acceptance page (candidate-facing, no auth)
Route::get('/offer/{token}', [PublicOfferController::class, 'show'])->name('public.offer');
Route::post('/offer/{token}/respond', [PublicOfferController::class, 'respond'])->name('public.offer.respond');

// Public interview self-scheduling (candidate-facing, no auth)
Route::get('/schedule/{token}', [PublicScheduleController::class, 'show'])->name('public.schedule');
Route::post('/schedule/{token}/book', [PublicScheduleController::class, 'book'])->name('public.schedule.book');
Route::get('/schedule/{token}/confirmed', [PublicScheduleController::class, 'confirmed'])->name('public.schedule.confirmed');

// Public post-interview pre-screening form (candidate-facing, no auth)
Route::get('/prescreen/{token}', [PublicPreScreeningController::class, 'show'])->name('public.prescreen');
Route::post('/prescreen/{token}', [PublicPreScreeningController::class, 'submit'])->name('public.prescreen.submit');
Route::get('/prescreen/{token}/application', [PublicPreScreeningController::class, 'showEmploymentApplication'])->name('public.prescreen.application');
Route::post('/prescreen/{token}/application', [PublicPreScreeningController::class, 'submitEmploymentApplication'])->name('public.prescreen.application.submit');

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
    Route::view('/calendar', 'hris.calendar')->name('calendar');
    Route::view('/screening', 'hris.screening')->name('screening');
    Route::view('/new-candidates', 'hris.new-candidates')->name('new-candidates');
    Route::view('/review-queue', 'hris.review-queue')->name('review-queue');
    Route::view('/offers', 'hris.offers')->name('offers');
    Route::view('/onboarding', 'hris.onboarding')->name('onboarding');
    Route::get('/workflow/pre-interview-questions', [HrisWorkflowController::class, 'preInterviewQuestions'])->name('workflow.pre_interview_questions');
    Route::get('/workflow/verifications-review', [HrisWorkflowController::class, 'verificationsReview'])->name('workflow.verifications_review');
    Route::get('/workflow/compliance-agreements', [HrisWorkflowController::class, 'complianceAgreements'])->name('workflow.compliance_agreements');
    Route::get('/workflow/clinical-staff-document', [HrisWorkflowController::class, 'clinicalStaffDocument'])->name('workflow.clinical_staff_document');
    Route::get('/workflow/emergency-contact', [HrisWorkflowController::class, 'emergencyContact'])->name('workflow.emergency_contact');
    Route::get('/workflow/training-development', [HrisWorkflowController::class, 'trainingDevelopment'])->name('workflow.training_development');
    Route::get('/workflow/financial-payroll-information', [HrisWorkflowController::class, 'financialPayrollInformation'])->name('workflow.financial_payroll_information');
    Route::get('/workflow/post-offer-documents', [HrisWorkflowController::class, 'postOfferDocuments'])->name('workflow.post_offer_documents');
    Route::get('/workflow/dwc-training', [HrisWorkflowController::class, 'dwcTraining'])->name('workflow.dwc_training');
    Route::get('/workflow/additional', [HrisWorkflowController::class, 'additional'])->name('workflow.additional');
    Route::view('/employee', 'hris.employee')->name('employees');
    Route::view('/timeoff', 'hris.timeoff')->name('timeoff');
    Route::view('/automations', 'hris.automations')->name('automations');
    Route::view('/settings', 'hris.settings')->name('settings');
    Route::get('/candidates/{candidate}/employment-application', [\App\Http\Controllers\CandidateApplicationController::class, 'editEmployment'])->name('candidate.employment-application.edit');
    Route::post('/candidates/{candidate}/employment-application', [\App\Http\Controllers\CandidateApplicationController::class, 'updateEmployment'])->name('candidate.employment-application.update');
    Route::get('/payrolls/{payroll}', [\App\Http\Controllers\PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('/payrolls/{payroll}/pdf', [\App\Http\Controllers\PayrollController::class, 'exportPdf'])->name('payrolls.pdf');
    Route::get('/candidates/{candidate}/application-print', [PrescreenPrintController::class, 'print'])->name('candidate.application.print');
});
