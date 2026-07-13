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

// Route 2FA
Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor/setup', [App\Http\Controllers\TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/enable', [App\Http\Controllers\TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable', [App\Http\Controllers\TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/resend', [App\Http\Controllers\TwoFactorController::class, 'resend'])->name('two-factor.resend');
});

// Route 2FA saat login (tidak pakai auth agar tidak infinite loop)
Route::get('/two-factor/verify', [App\Http\Controllers\TwoFactorController::class, 'verifyForm'])->name('two-factor.verify');
Route::post('/two-factor/verify', [App\Http\Controllers\TwoFactorController::class, 'verify'])->name('two-factor.verify.submit');

// ==================== ROUTE UNTUK VENDOR ====================
Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/vendor', [App\Http\Controllers\VendorController::class, 'index'])->name('vendor.dashboard');
    Route::get('/vendor/create-offer/{id}', [App\Http\Controllers\VendorController::class, 'createOffer'])->name('vendor.create-offer');
    Route::post('/vendor/store-offer/{id}', [App\Http\Controllers\VendorController::class, 'storeOffer'])->name('vendor.store-offer');
    Route::get('/vendor/history', [App\Http\Controllers\VendorController::class, 'history'])->name('vendor.history');
    Route::get('/vendor/show-offer/{id}', [App\Http\Controllers\VendorController::class, 'showOffer'])->name('vendor.show-offer');
});

// ==================== ROUTE PROFIL (SEMUA ROLE) ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // ==================== PERBAIKAN DI SINI ====================
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePasswordForm'])->name('profile.change-password.form');
    Route::put('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password.update');
    // =============================================================
    
    Route::post('/profile/upload-photo', [App\Http\Controllers\ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::delete('/profile/delete-photo', [App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
});

// ==================== ROUTE PROFIL ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // ==================== PERBAIKI INI ====================
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePasswordForm'])->name('profile.change-password.form');
    Route::put('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password.update');
    // ====================================================
    
    Route::post('/profile/upload-photo', [App\Http\Controllers\ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::delete('/profile/delete-photo', [App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
});

// ==================== ROUTE PROFIL ====================
Route::middleware(['auth'])->group(function () {
    // ==================== PERBAIKI DENGAN NAMA UNIK ====================
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePasswordForm'])->name('profile.change-password.form');
    Route::put('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password.update');
    // ================================================================
    
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-photo', [App\Http\Controllers\ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::delete('/profile/delete-photo', [App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
});

