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
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ==================== LANDING PAGE (PUBLIC - TIDAK PERLU LOGIN) ====================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/services', [LandingController::class, 'services'])->name('services');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');

// ==================== AUTHENTICATION (LOGIN, REGISTER, LOGOUT) ====================
require __DIR__.'/auth.php';

// ==================== REDIRECT AFTER LOGIN (DASHBOARD) ====================
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

// ==================== ROUTE PROFIL (SEMUA ROLE YANG SUDAH LOGIN) ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change-password');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
});

// ==================== ROUTE UNTUK USER BIASA ====================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user-dashboard', function () {
        return view('dashboard.user');
    })->name('user.dashboard');
    
    Route::get('/user/procurements', function () {
        $procurements = App\Models\ProcurementRequest::where('status', 'selesai')
                        ->latest()
                        ->paginate(10);
        return view('user.procurements', compact('procurements'));
    })->name('user.procurements');
    
    Route::get('/user/procurements/{id}', function ($id) {
        $procurement = App\Models\ProcurementRequest::with(['user', 'details.item'])
                        ->findOrFail($id);
        return view('user.procurement-detail', compact('procurement'));
    })->name('user.procurement-detail');
});

// ==================== ROUTE UNTUK ADMIN ====================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard Admin
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    
    // CRUD User Management
    Route::resource('admin/users', UserController::class);
});

// ==================== ROUTE UNTUK BAGIAN PENGAADAAN ====================
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

// ==================== ROUTE UNTUK DATA MASTER (KATEGORI, SUPPLIER, BARANG) ====================
Route::middleware(['auth', 'role:admin|pengadaan'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('items', ItemController::class);
});

// ==================== ROUTE UNTUK PENGAJUAN PENGADAAN ====================
Route::middleware(['auth', 'role:pengadaan|admin'])->group(function () {
    Route::resource('procurements', ProcurementRequestController::class);
    Route::post('/procurements/{id}/submit', [ProcurementRequestController::class, 'submit'])->name('procurements.submit');
    Route::get('/get-item/{id}', [ProcurementRequestController::class, 'getItem'])->name('get.item');
});

// ==================== ROUTE UNTUK APPROVAL (PERSETUJUAN PIMPINAN) ====================
Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/history', [ApprovalController::class, 'history'])->name('approvals.history');
    Route::get('/approvals/{id}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
});

// ==================== ROUTE UNTUK VENDOR QUOTE (PENAWARAN VENDOR) ====================
Route::middleware(['auth', 'role:pengadaan|admin'])->group(function () {
    Route::get('/vendor-quotes', [VendorQuoteController::class, 'index'])->name('vendor-quotes.index');
    Route::get('/vendor-quotes/{id}', [VendorQuoteController::class, 'show'])->name('vendor-quotes.show');
    Route::post('/vendor-quotes/{id}', [VendorQuoteController::class, 'store'])->name('vendor-quotes.store');
    Route::post('/vendor-quotes/{procurementId}/select/{quoteId}', [VendorQuoteController::class, 'selectVendor'])->name('vendor-quotes.select');
    Route::post('/vendor-quotes/{id}/complete', [VendorQuoteController::class, 'complete'])->name('vendor-quotes.complete');
    Route::delete('/vendor-quotes/{procurementId}/{quoteId}', [VendorQuoteController::class, 'destroy'])->name('vendor-quotes.destroy');
});

// ==================== ROUTE UNTUK LAPORAN ====================
Route::middleware(['auth', 'role:admin|pengadaan|pimpinan'])->group(function () {
    Route::get('/reports/procurements', [ReportController::class, 'procurementReport'])->name('reports.procurements');
    Route::get('/reports/procurements/export', [ReportController::class, 'exportProcurementReport'])->name('reports.procurements.export');
    Route::get('/reports/vendors', [ReportController::class, 'vendorReport'])->name('reports.vendors');
    Route::get('/reports/vendors/export', [ReportController::class, 'exportVendorReport'])->name('reports.vendors.export');
});

// ==================== ROUTE UNTUK VENDOR ====================
Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/vendor', [App\Http\Controllers\VendorController::class, 'index'])->name('vendor.dashboard');
    Route::get('/vendor/create-offer/{id}', [App\Http\Controllers\VendorController::class, 'createOffer'])->name('vendor.create-offer');
    Route::post('/vendor/store-offer/{id}', [App\Http\Controllers\VendorController::class, 'storeOffer'])->name('vendor.store-offer');
    Route::get('/vendor/history', [App\Http\Controllers\VendorController::class, 'history'])->name('vendor.history');
    Route::get('/vendor/show-offer/{id}', [App\Http\Controllers\VendorController::class, 'showOffer'])->name('vendor.show-offer');
});

Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/approvals', [App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/history', [App\Http\Controllers\ApprovalController::class, 'history'])->name('approvals.history');
    Route::get('/approvals/{id}', [App\Http\Controllers\ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{id}/approve', [App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');
});

// ==================== ROUTE UNTUK USER BIASA ====================
Route::middleware(['auth', 'role:user'])->group(function () {
    // Dashboard
    Route::get('/user-dashboard', [App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
    
    // Informasi Pengadaan
    Route::get('/user/procurements', [App\Http\Controllers\UserController::class, 'procurements'])->name('user.procurements');
    Route::get('/user/procurement/{id}', [App\Http\Controllers\UserController::class, 'showProcurement'])->name('user.procurement-detail');
    
    // Pendaftaran Vendor
    Route::get('/user/register-vendor', [App\Http\Controllers\UserController::class, 'registerVendorForm'])->name('user.register-vendor');
    Route::post('/user/register-vendor', [App\Http\Controllers\UserController::class, 'registerVendor'])->name('user.register-vendor.store');
    
    // Tracking Perkembangan Pengadaan
    Route::get('/user/track-progress', [App\Http\Controllers\UserController::class, 'trackProgress'])->name('user.track-progress');
    Route::get('/user/track-detail/{id}', [App\Http\Controllers\UserController::class, 'trackDetail'])->name('user.track-detail');
});

// Route untuk User (batalkan pendaftaran vendor)
Route::middleware(['auth', 'role:user'])->group(function () {
    // ... route lainnya
    Route::delete('/user/cancel-vendor-registration', [App\Http\Controllers\UserController::class, 'cancelVendorRegistration'])->name('user.cancel-vendor-registration');
});

// Route untuk Admin (Verifikasi Vendor)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/vendors/verification', [App\Http\Controllers\Admin\VendorVerificationController::class, 'index'])->name('vendors.verification');
    Route::get('/vendors/{id}', [App\Http\Controllers\Admin\VendorVerificationController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{id}/approve', [App\Http\Controllers\Admin\VendorVerificationController::class, 'approve'])->name('vendors.approve');
    Route::post('/vendors/{id}/reject', [App\Http\Controllers\Admin\VendorVerificationController::class, 'reject'])->name('vendors.reject');
    Route::delete('/vendors/{id}', [App\Http\Controllers\Admin\VendorVerificationController::class, 'destroy'])->name('vendors.destroy');
});

// Route untuk Supplier (Verifikasi Vendor)
Route::middleware(['auth', 'role:admin|pengadaan'])->group(function () {
    Route::resource('suppliers', SupplierController::class);
    // Route untuk verifikasi (hanya admin yang bisa)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/suppliers/{id}/show', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::post('/suppliers/{id}/verify', [SupplierController::class, 'verify'])->name('suppliers.verify');
        Route::post('/suppliers/{id}/reject', [SupplierController::class, 'reject'])->name('suppliers.reject');
    });
});


Route::get('/api-test', function () {
    return view('api-test');
});

// API Internal (AJAX) - untuk form dynamic
Route::get('/get-item/{id}', [App\Http\Controllers\ProcurementRequestController::class, 'getItem'])->name('get.item');

// Route untuk Laporan
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/procurements', [ReportController::class, 'procurementReport'])->name('reports.procurements');
    Route::get('/reports/procurements/export', [ReportController::class, 'exportProcurementReport'])->name('reports.procurements.export');
    Route::get('/reports/procurements/pdf', [ReportController::class, 'exportProcurementPDF'])->name('reports.procurements.pdf'); // TAMBAHKAN INI
    Route::get('/reports/vendors', [ReportController::class, 'vendorReport'])->name('reports.vendors');
    Route::get('/reports/vendors/export', [ReportController::class, 'exportVendorReport'])->name('reports.vendors.export');
    Route::get('/reports/vendors/pdf', [ReportController::class, 'exportVendorPDF'])->name('reports.vendors.pdf'); // TAMBAHKAN INI
});

// Route untuk Vendor Quote (Pengadaan & Admin)
Route::middleware(['auth', 'role:pengadaan|admin'])->group(function () {
    Route::get('/vendor-quotes', [VendorQuoteController::class, 'index'])->name('vendor-quotes.index');
    Route::get('/vendor-quotes/{id}', [VendorQuoteController::class, 'show'])->name('vendor-quotes.show');
    Route::post('/vendor-quotes/{id}', [VendorQuoteController::class, 'store'])->name('vendor-quotes.store');
    Route::post('/vendor-quotes/{procurementId}/select/{quoteId}', [VendorQuoteController::class, 'selectVendor'])->name('vendor-quotes.select');
    Route::post('/vendor-quotes/{id}/complete', [VendorQuoteController::class, 'complete'])->name('vendor-quotes.complete');
    Route::delete('/vendor-quotes/{procurementId}/{quoteId}', [VendorQuoteController::class, 'destroy'])->name('vendor-quotes.destroy');
});

// Route untuk Pengajuan Pengadaan
Route::middleware(['auth', 'role:pengadaan|admin'])->group(function () {
    Route::resource('procurements', ProcurementRequestController::class);
    Route::post('/procurements/{id}/submit', [ProcurementRequestController::class, 'submit'])->name('procurements.submit');
    Route::post('/procurements/{id}/complete', [ProcurementRequestController::class, 'complete'])->name('procurements.complete'); // TAMBAHKAN INI
    Route::get('/get-item/{id}', [ProcurementRequestController::class, 'getItem'])->name('get.item');
});

// Route untuk User Biasa
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user-dashboard', function () {
        return view('dashboard.user');
    })->name('user.dashboard');
});

// Route untuk User Biasa
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user-dashboard', [App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
    
    // Informasi Pengadaan
    Route::get('/user/procurements', [App\Http\Controllers\UserController::class, 'procurements'])->name('user.procurements');
    Route::get('/user/procurement/{id}', [App\Http\Controllers\UserController::class, 'showProcurement'])->name('user.procurement-detail');
    
    // ==================== REGISTER VENDOR ====================
    Route::get('/user/register-vendor', [App\Http\Controllers\UserController::class, 'registerVendorForm'])->name('user.register-vendor');
    Route::post('/user/register-vendor', [App\Http\Controllers\UserController::class, 'registerVendor'])->name('user.register-vendor.store');
    
    // Tracking
    Route::get('/user/track-progress', [App\Http\Controllers\UserController::class, 'trackProgress'])->name('user.track-progress');
    Route::get('/user/track-detail/{id}', [App\Http\Controllers\UserController::class, 'trackDetail'])->name('user.track-detail');
    
    // Batalkan pendaftaran vendor
    Route::delete('/user/cancel-vendor-registration', [App\Http\Controllers\UserController::class, 'cancelVendorRegistration'])->name('user.cancel-vendor-registration');
});

// ==================== ROUTE 2FA ====================
Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::get('/two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate');
});

// Route 2FA saat login (tanpa middleware two-factor agar tidak infinite loop)
Route::get('/two-factor/verify', [TwoFactorController::class, 'verifyForm'])->name('two-factor.verify');
Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify.submit');

// ==================== ROUTE DASHBOARD DENGAN 2FA ====================
Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/pengadaan', [DashboardController::class, 'pengadaanDashboard'])->name('pengadaan.dashboard');
    Route::get('/pimpinan', [DashboardController::class, 'pimpinanDashboard'])->name('pimpinan.dashboard');
    Route::get('/vendor', [DashboardController::class, 'vendorDashboard'])->name('vendor.dashboard');
    Route::get('/user-dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
});