<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIPADU
|--------------------------------------------------------------------------
*/

// ============ PUBLIC / AUTH ============
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============ PARENT (ORANG TUA) ROUTES ============
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/complaints/create', [\App\Http\Controllers\Parent\ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [\App\Http\Controllers\Parent\ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints', [\App\Http\Controllers\Parent\ComplaintController::class, 'index'])->name('complaints.index');
    Route::post('/complaints/{complaint}/rate', [\App\Http\Controllers\Parent\ComplaintController::class, 'rate'])->name('complaints.rate');
});

// ============ ADMIN ROUTES ============
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Complaints management
    Route::get('/complaints', [\App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('complaints.index');
    Route::post('/complaints/{complaint}/verify', [\App\Http\Controllers\Admin\ComplaintController::class, 'verify'])->name('complaints.verify');
    Route::post('/complaints/{complaint}/status', [\App\Http\Controllers\Admin\ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');

    // Master data
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'show', 'edit']);
});

// ============ PRINCIPAL (KEPALA SEKOLAH) ROUTES ============
Route::prefix('principal')->name('principal.')->middleware(['auth', 'role:principal'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Principal\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ahp', [\App\Http\Controllers\Principal\AhpController::class, 'index'])->name('ahp.index');
    Route::get('/reports/complaints', [\App\Http\Controllers\Principal\ReportController::class, 'complaints'])->name('reports.complaints');
    Route::get('/reports/evaluation', [\App\Http\Controllers\Principal\ReportController::class, 'evaluation'])->name('reports.evaluation');
});
