<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web as WEB;

/**
 * This file contains route definitions that map new terminology to existing controllers
 * It allows URLs to use the new terminology while keeping the existing codebase intact
 * 
 * Terminology changes:
 * - Company → Organisation
 * - Candidates → Applicants
 * - Employers → Organisation
 * - Job → Opportunity
 */

// Company → Organisation routes
Route::resource('/organisations', WEB\CompanyController::class)
    ->only(['index', 'show'])
    ->names('organisations');

// Candidates → Applicants routes
Route::get('/applicants/{candidate}/visit', [WEB\CandidateController::class, 'visit'])->name('applicants.visit');
Route::get('/applicants/profile', [WEB\CandidateController::class, 'profile']);
Route::resource('/applicants', WEB\CandidateController::class)
    ->only(['index', 'show'])
    ->names('applicants');
Route::post('/applicants/{user}/bookmark', [WEB\CandidateController::class, 'toggleBookmark'])
    ->name('applicants.bookmark')
    ->middleware('auth');
Route::post('/applicants/{candidate}/send-mail', [WEB\CandidateController::class, 'sendMail'])
    ->name('applicants.send-mail');

// Job → Opportunity routes
Route::resource('/opportunities', WEB\JobController::class)
    ->only(['index', 'show'])
    ->names('opportunities');
Route::post('/opportunities/{job}/bookmark', [WEB\JobController::class, 'toggleBookmark'])
    ->name('opportunities.bookmark')
    ->middleware('auth');
Route::get('/opportunities/{job}/apply', [WEB\JobController::class, 'apply'])
    ->name('opportunities.apply')
    ->middleware('auth');
Route::post('/opportunities/{job}/apply', [WEB\JobController::class, 'applyStore'])
    ->name('opportunities.apply')
    ->middleware('auth');

// Job categories/services → Opportunity categories/services
Route::get('opportunity-category/{slug}', [WEB\JobController::class, 'index'])
    ->name('opportunity-categories.show');
Route::get('opportunity-service/{slug}', [WEB\JobController::class, 'index'])
    ->name('opportunity-services.show');

// Employer → Organisation routes (user panel)
Route::group(['prefix' => 'organisation', 'as' => 'organisation.', 'middleware' => ['auth', 'email_verified', 'active_account', 'company_info', 'employer', 'saas']], function () {
    
    Route::middleware('kyc_verified')->group(function () {
        Route::post('opportunity-application/seen', [App\Http\Controllers\Employer\JobController::class, 'updateSeenAt'])
            ->name('opportunity-application-seen');
        Route::resource('opportunities', App\Http\Controllers\Employer\JobController::class)
            ->names('opportunities');
        Route::get('opportunity-applicants', [App\Http\Controllers\Employer\JobController::class, 'applicants'])
            ->name('opportunity-applicants');
        Route::get('opportunity-reviews', [App\Http\Controllers\Employer\JobController::class, 'reviews'])
            ->name('opportunity-reviews');
        Route::resource('applicant-reviews', App\Http\Controllers\Employer\CandidateReviewController::class)
            ->only('store')
            ->names('applicant-reviews');
        Route::post('hire-applicant', [App\Http\Controllers\Employer\EmployerPanelController::class, 'hireCandidate'])
            ->name('hire-applicant');
    });

    // Redirect dashboard and other common routes
    Route::get('dashboard', function() {
        return redirect()->route('employer.dashboard');
    })->name('dashboard');
    
    Route::get('saved-applicants', function() {
        return redirect()->route('employer.saved-candidates');
    })->name('saved-applicants');
});
