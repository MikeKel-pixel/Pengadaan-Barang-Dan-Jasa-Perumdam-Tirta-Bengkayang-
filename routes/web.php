<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\VendorQuoteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ==================== LANDING PAGE ====================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/services', [LandingController::class, 'services'])->name('services');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');

require __DIR__.'/auth.php';

// ==================== DASHBOARD REDIRECT ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return redirect('/admin');
        } elseif ($user->hasRole('pengadaan')) {
            return redirect('/pengadaan');
        } elseif ($user->hasRole('pimpinan')) {
            return redirect('/pimpinan');
        } elseif ($user->hasRole('vendor')) {
            return redirect('/vendor');
        } elseif ($user->hasRole('user')) {
            return redirect('/user-dashboard');
        }
        return redirect('/');
    })->name('dashboard');
});

// ==================== ROUTE PROFIL ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change-password.form');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password.update');
    Route::post('/profile/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
});

// ==================== ROUTE UNTUK USER ====================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user-dashboard', [App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user/procurements', [App\Http\Controllers\UserController::class, 'procurements'])->name('user.procurements');
    Route::get('/user/procurement/{id}', [App\Http\Controllers\UserController::class, 'showProcurement'])->name('user.procurement-detail');
    Route::get('/user/register-vendor', [App\Http\Controllers\UserController::class, 'registerVendorForm'])->name('user.register-vendor');
    Route::post('/user/register-vendor', [App\Http\Controllers\UserController::class, 'registerVendor'])->name('user.register-vendor.store');
    Route::get('/user/track-progress', [App\Http\Controllers\UserController::class, 'trackProgress'])->name('user.track-progress');
    Route::get('/user/track-detail/{id}', [App\Http\Controllers\UserController::class, 'trackDetail'])->name('user.track-detail');
    Route::delete('/user/cancel-vendor-registration', [App\Http\Controllers\UserController::class, 'cancelVendorRegistration'])->name('user.cancel-vendor-registration');
});

// ==================== ROUTE UNTUK ADMIN ====================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
});

// ==================== ROUTE UNTUK PENGAADAAN ====================
Route::middleware(['auth', 'role:pengadaan'])->group(function () {
    Route::get('/pengadaan', [DashboardController::class, 'pengadaanDashboard'])->name('pengadaan.dashboard');
});

// ==================== ROUTE UNTUK PIMPINAN ====================
Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/pimpinan', [DashboardController::class, 'pimpinanDashboard'])->name('pimpinan.dashboard');
});

// ==================== ROUTE UNTUK VENDOR ====================
Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/vendor', [DashboardController::class, 'vendorDashboard'])->name('vendor.dashboard');
});

// ==================== ROUTE UNTUK DATA MASTER ====================
Route::middleware(['auth', 'role:admin|pengadaan'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('items', ItemController::class);
});

// ==================== ROUTE UNTUK PENGAJUAN ====================
Route::middleware(['auth', 'role:pengadaan|admin'])->group(function () {
    Route::resource('procurements', ProcurementRequestController::class);
    Route::post('/procurements/{id}/submit', [ProcurementRequestController::class, 'submit'])->name('procurements.submit');
    Route::get('/get-item/{id}', [ProcurementRequestController::class, 'getItem'])->name('get.item');
});

// ==================== ROUTE UNTUK APPROVAL ====================
Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/history', [ApprovalController::class, 'history'])->name('approvals.history');
    Route::get('/approvals/{id}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
});

// ==================== ROUTE UNTUK VENDOR QUOTE ====================
Route::middleware(['auth', 'role:pengadaan|admin'])->group(function () {
    Route::get('/vendor-quotes', [VendorQuoteController::class, 'index'])->name('vendor-quotes.index');
    Route::get('/vendor-quotes/{id}', [VendorQuoteController::class, 'show'])->name('vendor-quotes.show');
    Route::post('/vendor-quotes/{id}', [VendorQuoteController::class, 'store'])->name('vendor-quotes.store');
    Route::post('/vendor-quotes/{procurementId}/select/{quoteId}', [VendorQuoteController::class, 'selectVendor'])->name('vendor-quotes.select');
    Route::post('/vendor-quotes/{id}/complete', [VendorQuoteController::class, 'complete'])->name('vendor-quotes.complete');
    Route::delete('/vendor-quotes/{procurementId}/{quoteId}', [VendorQuoteController::class, 'destroy'])->name('vendor-quotes.destroy');
});

// ==================== ROUTE UNTUK LAPORAN ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/procurements', [ReportController::class, 'procurementReport'])->name('reports.procurements');
    Route::get('/reports/procurements/export', [ReportController::class, 'exportProcurementReport'])->name('reports.procurements.export');
    Route::get('/reports/vendors', [ReportController::class, 'vendorReport'])->name('reports.vendors');
    Route::get('/reports/vendors/export', [ReportController::class, 'exportVendorReport'])->name('reports.vendors.export');
});