<?php

use App\Http\Controllers\FacilityAdmin\ClinicalNoteController;
use App\Http\Controllers\FacilityAdmin\DashboardController;
use App\Http\Controllers\FacilityAdmin\FacilitySettingsController;
use App\Http\Controllers\FacilityAdmin\PatientLinkController;
use App\Http\Controllers\FacilityAdmin\ReferralController;
use App\Http\Controllers\FacilityAdmin\ReportController;
use App\Http\Controllers\FacilityAdmin\StaffController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Facility Admin Routes (B2B)
|--------------------------------------------------------------------------
|
| Routes for B2B facility administration. These routes are protected by
| authentication, active subscription, and facility access middleware.
|
*/

Route::middleware(['auth', 'verified', 'tenant.active'])->prefix('facility')->name('facility.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Staff Management (requires staff_admin or doctor role)
    Route::middleware('staff.role:admin,doctor')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // Patient Links (requires staff_admin, doctor, midwife, or nurse)
    Route::middleware('staff.role:admin,doctor,midwife,nurse')->group(function () {
        Route::get('/patients', [PatientLinkController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientLinkController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientLinkController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patientLink}', [PatientLinkController::class, 'show'])->name('patients.show');
        Route::put('/patients/{patientLink}', [PatientLinkController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{patientLink}', [PatientLinkController::class, 'destroy'])->name('patients.destroy');
        Route::post('/patients/{patientLink}/revoke', [PatientLinkController::class, 'revoke'])->name('patients.revoke');
        Route::post('/patients/{patientLink}/send-invitation', [PatientLinkController::class, 'sendInvitation'])->name('patients.send-invitation');
        Route::post('/patients/{patientLink}/claim-profile', [PatientLinkController::class, 'claimProfile'])->name('patients.claim-profile');
    });

    // Clinical Notes (requires doctor, midwife, or nurse)
    Route::middleware('staff.role:doctor,midwife,nurse')->group(function () {
        Route::get('/clinical-notes', [ClinicalNoteController::class, 'index'])->name('clinical-notes.index');
        Route::get('/clinical-notes/create', [ClinicalNoteController::class, 'create'])->name('clinical-notes.create');
        Route::post('/clinical-notes', [ClinicalNoteController::class, 'store'])->name('clinical-notes.store');
        Route::get('/clinical-notes/{clinicalNote}', [ClinicalNoteController::class, 'show'])->name('clinical-notes.show');
        Route::get('/clinical-notes/{clinicalNote}/edit', [ClinicalNoteController::class, 'edit'])->name('clinical-notes.edit');
        Route::put('/clinical-notes/{clinicalNote}', [ClinicalNoteController::class, 'update'])->name('clinical-notes.update');
        Route::delete('/clinical-notes/{clinicalNote}', [ClinicalNoteController::class, 'destroy'])->name('clinical-notes.destroy');
    });

    // Referrals (requires doctor, midwife, or admin)
    Route::middleware('staff.role:admin,doctor,midwife')->group(function () {
        Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
        Route::get('/referrals/create', [ReferralController::class, 'create'])->name('referrals.create');
        Route::post('/referrals', [ReferralController::class, 'store'])->name('referrals.store');
        Route::get('/referrals/{referral}', [ReferralController::class, 'show'])->name('referrals.show');
        Route::post('/referrals/{referral}/accept', [ReferralController::class, 'accept'])->name('referrals.accept');
        Route::post('/referrals/{referral}/complete', [ReferralController::class, 'complete'])->name('referrals.complete');
        Route::post('/referrals/{referral}/cancel', [ReferralController::class, 'cancel'])->name('referrals.cancel');
    });

    // Reports (requires staff_admin role)
    Route::middleware('staff.role:admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/clinical-notes', [ReportController::class, 'clinicalNotes'])->name('reports.clinical-notes');
        Route::get('/reports/patients', [ReportController::class, 'patients'])->name('reports.patients');
    });

    // Facility Settings (requires staff_admin role)
    Route::middleware('staff.role:admin')->group(function () {
        Route::get('/settings', [FacilitySettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [FacilitySettingsController::class, 'update'])->name('settings.update');
    });
});
