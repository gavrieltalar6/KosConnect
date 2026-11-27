<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pemilik\DashboardController as PemilikDashboardController;
use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboardController;
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboardController;
use App\Http\Controllers\Pengurus\PaymentController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\Penghuni\InvoiceController as PenghuniInvoiceController;

// Redirect halaman utama (/) ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::redirect('/', '/login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['role:pemilik,pengurus'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('invoices', [\App\Http\Controllers\Pemilik\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [\App\Http\Controllers\Pemilik\InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [\App\Http\Controllers\Pemilik\InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{invoice}/payment', [PaymentController::class, 'create'])->name('invoices.payment.create');
    Route::post('invoices/{invoice}/payment', [PaymentController::class, 'store'])->name('invoices.payment.store');

    // Manajemen Penghuni (Pemilik & Pengurus dapat melihat, membuat akun penghuni, dan mengubah data)
    Route::resource('tenants', \App\Http\Controllers\Pemilik\TenantController::class)->except(['destroy']);
});

Route::middleware(['role:pemilik,pengurus'])->group(function () {
    Route::get('password-resets', [\App\Http\Controllers\PasswordResetController::class, 'index'])->name('password_resets.index');
    Route::get('password-resets/{user}/edit', [\App\Http\Controllers\PasswordResetController::class, 'edit'])->name('password_resets.edit');
    Route::put('password-resets/{user}', [\App\Http\Controllers\PasswordResetController::class, 'update'])->name('password_resets.update');
    // Alias route tanpa prefix pemilik untuk kompatibilitas
    Route::get('invoices/{invoice}/payment', [PaymentController::class, 'create'])->name('invoices.payment.create');
    Route::post('invoices/{invoice}/payment', [PaymentController::class, 'store'])->name('invoices.payment.store');
});

// Webhook Notification dari Midtrans (di luar middleware role; CSRF dikecualikan di bootstrap/app.php)
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])->name('midtrans.notification');

Route::middleware(['role:pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/dashboard', PemilikDashboardController::class)->name('dashboard');
    Route::get('/damage-reports', [\App\Http\Controllers\Pemilik\DamageReportController::class, 'index'])->name('damage_reports.index');
    Route::resource('rooms', \App\Http\Controllers\Pemilik\RoomController::class)->except(['show']);
    Route::delete('tenants/{tenant}', [\App\Http\Controllers\Pemilik\TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::resource('invoices', \App\Http\Controllers\Pemilik\InvoiceController::class)->except(['index', 'create', 'store']);
});

Route::middleware(['role:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', PengurusDashboardController::class)->name('dashboard');
    Route::get('/cleaning', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'index'])->name('cleaning.index');
    Route::get('/cleaning/report', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'report'])->name('cleaning.report');
    Route::put('/cleaning/{schedule}', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'update'])->name('cleaning.update');
    Route::post('/cleaning/{schedule}/done', [\App\Http\Controllers\Pengurus\CleaningReviewController::class, 'markAsDone'])->name('cleaning.markAsDone');

    Route::get('/damage-reports', [\App\Http\Controllers\Pengurus\DamageReportController::class, 'index'])->name('damage_reports.index');
    Route::get('/damage-reports/history', [\App\Http\Controllers\Pengurus\DamageReportController::class, 'history'])->name('damage_reports.history');
    Route::put('/damage-reports/{report}', [\App\Http\Controllers\Pengurus\DamageReportController::class, 'update'])->name('damage_reports.update');
    
});

Route::middleware(['role:penghuni'])->prefix('penghuni')->name('penghuni.')->group(function () {
    Route::get('/dashboard', PenghuniDashboardController::class)->name('dashboard');
    Route::get('/invoices', [PenghuniInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{invoice}/snap-token', [PenghuniInvoiceController::class, 'snapToken'])->name('invoices.snap-token');
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
});