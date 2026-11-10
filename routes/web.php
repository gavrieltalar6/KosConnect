<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pemilik\DashboardController as PemilikDashboardController;
use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboardController;
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboardController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['role:pemilik,pengurus'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('invoices', [\App\Http\Controllers\Pemilik\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}/payment', [\App\Http\Controllers\Pengurus\PaymentController::class, 'create'])->name('invoices.payment.create');
    Route::post('invoices/{invoice}/payment', [\App\Http\Controllers\Pengurus\PaymentController::class, 'store'])->name('invoices.payment.store');
});

Route::middleware(['role:pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/dashboard', PemilikDashboardController::class)->name('dashboard');
    Route::resource('rooms', \App\Http\Controllers\Pemilik\RoomController::class)->except(['show']);
    Route::resource('tenants', \App\Http\Controllers\Pemilik\TenantController::class);
    Route::resource('invoices', \App\Http\Controllers\Pemilik\InvoiceController::class)->except(['index']);
});

Route::middleware(['role:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', PengurusDashboardController::class)->name('dashboard');
    Route::get('/cleaning', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'index'])->name('cleaning.index');
    Route::get('/cleaning/report', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'report'])->name('cleaning.report');
    Route::put('/cleaning/{schedule}', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'update'])->name('cleaning.update');
    Route::post('/cleaning/{schedule}/done', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'markAsDone'])->name('cleaning.markAsDone');

    Route::get('/damage-reports', [\App\Http\Controllers\Pengurus\DamageReportController::class, 'index'])->name('damage_reports.index');
    Route::put('/damage-reports/{report}', [\App\Http\Controllers\Pengurus\DamageReportController::class, 'update'])->name('damage_reports.update');
});

Route::middleware(['role:penghuni'])->prefix('penghuni')->name('penghuni.')->group(function () {
    Route::get('/dashboard', PenghuniDashboardController::class)->name('dashboard');
    Route::get('/cleaning', [\App\Http\Controllers\Penghuni\CleaningScheduleController::class, 'index'])->name('cleaning.index');
    Route::get('/cleaning/report', [\App\Http\Controllers\Penghuni\CleaningScheduleController::class, 'report'])->name('cleaning.report');
    Route::get('/cleaning/create', [\App\Http\Controllers\Penghuni\CleaningScheduleController::class, 'create'])->name('cleaning.create');
    Route::post('/cleaning', [\App\Http\Controllers\Penghuni\CleaningScheduleController::class, 'store'])->name('cleaning.store');
    Route::get('/cleaning/{schedule}/edit', [\App\Http\Controllers\Penghuni\CleaningScheduleController::class, 'edit'])->name('cleaning.edit');
    Route::put('/cleaning/{schedule}', [\App\Http\Controllers\Penghuni\CleaningScheduleController::class, 'update'])->name('cleaning.update');
    Route::delete('/cleaning/{schedule}', [\App\Http\Controllers\Penghuni\CleaningScheduleController::class, 'destroy'])->name('cleaning.destroy');

    Route::get('/damage-reports', [\App\Http\Controllers\Penghuni\DamageReportController::class, 'index'])->name('damage_reports.index');
    Route::get('/damage-reports/create', [\App\Http\Controllers\Penghuni\DamageReportController::class, 'create'])->name('damage_reports.create');
    Route::post('/damage-reports', [\App\Http\Controllers\Penghuni\DamageReportController::class, 'store'])->name('damage_reports.store');
    Route::patch('/damage-reports/{report}/cancel', [\App\Http\Controllers\Penghuni\DamageReportController::class, 'cancel'])->name('damage_reports.cancel');
});

Route::get('/penghuni', function () {
    return redirect()->route('penghuni.dashboard');
})->name('penghuni.dashboard');