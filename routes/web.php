<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChangedayController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PerformaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BreakController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Karyawan\DashboardController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});


// Default dashboard route for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin() || auth()->user()->isHR()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('karyawan.dashboard');
    })->name('dashboard');
});

// Employee Dashboard Attendance Filter Route BY KARYAWAN
Route::get('/attendance/filter', [KaryawanDashboardController::class, 'filterAttendance'])->name('attendance.filter');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/profile/performance-chart-data', [ProfileController::class, 'performanceChartData'])->name('profile.performance-chart-data');
});

// Calendar Routes
Route::middleware('auth')->prefix('calendar')->name('calendar.')->group(function () {
    Route::get('/events', [CalendarController::class, 'getEvents'])->name('events');
    Route::get('/event-detail', [CalendarController::class, 'getEventDetail'])->name('event-detail');
});

// Performa Routes (Employee - View only)
Route::middleware('auth')
    ->prefix('performa')
    ->name('performa.')
    ->group(function () {
        Route::get('/', [PerformaController::class, 'index'])->name('index');
        Route::get('/{id}', [PerformaController::class, 'show'])->name('show');
    });

// Absensi Routes (for employees)
Route::middleware('auth')->group(function () {
    Route::prefix('absensi')
        ->name('absensi.')
        ->group(function () {
            Route::get('/', [AbsensiController::class, 'index'])->name('index');
            Route::get('/create', [AbsensiController::class, 'create'])->name('create');
            Route::post('/', [AbsensiController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AbsensiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AbsensiController::class, 'update'])->name('update');
            Route::post('/{id}/pulang', [AbsensiController::class, 'absensiPulang'])->name('pulang');
            Route::delete('/{id}', [AbsensiController::class, 'cancelChangeDay'])->name('destroy');
            Route::get('/{id}', [AbsensiController::class, 'show'])->name('show');
        });
});

// Cuti Routes (Employee)
Route::middleware('auth')
    ->prefix('cuti')
    ->name('cuti.')
    ->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{id}', [LeaveController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LeaveController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LeaveController::class, 'update'])->name('update');
        Route::delete('/{id}', [LeaveController::class, 'destroy'])->name('destroy');
    });

// CHANGE DAY Routes (Employee)
Route::middleware('auth')->group(function () {
    Route::prefix('changeday')
        ->name('changeday.')
        ->group(function () {
            Route::get('/', [ChangedayController::class, 'indexEmployee'])->name('index');
            Route::post('/request', [ChangedayController::class, 'requestChangeDay'])->name('request');
            Route::get('/{id}', [ChangedayController::class, 'show'])->name('show');
            Route::put('/{id}', [ChangedayController::class, 'updateRequest'])->name('update');
            Route::delete('/{id}', [ChangedayController::class, 'cancelRequest'])->name('cancel');
        });
});

// Pengumuman Routes (for employees to view)
Route::middleware('auth')->group(function () {
    Route::prefix('pengumuman')
        ->name('pengumuman.')
        ->group(function () {
            Route::get('/', [PengumumanController::class, 'employeeIndex'])->name('index');
            Route::get('/{id}', [PengumumanController::class, 'employeeShow'])->name('show');
        });
});

// Notifikasi Routes (for all authenticated users)
Route::middleware('auth')->group(function () {
    Route::prefix('notifikasi')
        ->name('notifikasi.')
        ->group(function () {
            Route::get('/', [NotifikasiController::class, 'index'])->name('index');
            Route::get('/page', [NotifikasiController::class, 'page'])->name('page');
            Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('mark-read');
            Route::post('/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/unread-count', [NotifikasiController::class, 'getUnreadCount'])->name('unread-count');
        });
});

// Penggajian Routes (Employee)
Route::middleware('auth')
    ->prefix('penggajian')
    ->name('penggajian.')
    ->group(function () {
        Route::get('/', [PenggajianController::class, 'index'])->name('index');
        Route::get('/{id}', [PenggajianController::class, 'show'])->name('show');
        Route::get('/{id}/download', [PenggajianController::class, 'downloadPayslip'])->name('download');
    });

// Admin/HR Routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Employee Management
        Route::get('/karyawan', [AdminDashboardController::class, 'karyawan'])->name('karyawan');
        Route::post('/karyawan', [AdminDashboardController::class, 'storeKaryawan'])->name('karyawan.store');
        Route::get('/karyawan/{id}/edit', [AdminDashboardController::class, 'editKaryawan'])->name('karyawan.edit');
        Route::put('/karyawan/{id}', [AdminDashboardController::class, 'updateKaryawan'])->name('karyawan.update');
        Route::get('/karyawan/{id}/show-password', [AdminDashboardController::class, 'showPassword'])->name('karyawan.show-password');
        Route::get('/karyawan/{id}/employee-detail', [AdminDashboardController::class, 'getEmployeeDetail'])->name('karyawan.employee-detail');
        Route::get('/karyawan/detail/{id}', [AdminDashboardController::class, 'karyawanDetail'])->name('karyawan.detail');

        // Absensi Management for Admin/HR
        Route::prefix('absensi')
            ->name('absensi.')
            ->group(function () {
                Route::get('/', [AbsensiController::class, 'adminIndex'])->name('index');
                Route::put('/{id}/status', [AbsensiController::class, 'adminUpdateStatusChangeDay'])->name('update-status-change-day');
                Route::put('/{id}/status-absensi', [AbsensiController::class, 'adminUpdateStatusAbsensi'])->name('update-status-absensi');
                Route::get('/{id}', [AbsensiController::class, 'adminShow'])->name('show');
            });

        // Leave Management for Admin/HR
        Route::prefix('leave')
            ->name('leave.')
            ->group(function () {
                Route::get('/', [LeaveController::class, 'adminIndex'])->name('index');
                Route::get('/{id}', [LeaveController::class, 'adminShow'])->name('show');
                Route::put('/{id}/status', [LeaveController::class, 'adminUpdateStatus'])->name('update-status');
            });

        Route::prefix('penggajian')
            ->name('penggajian.')
            ->group(function () {
                Route::get('/', [PenggajianController::class, 'adminIndex'])->name('index');
                Route::get('/create', [PenggajianController::class, 'adminCreate'])->name('create');
                Route::post('/', [PenggajianController::class, 'adminStore'])->name('store');
                Route::get('/export/report', [PenggajianController::class, 'exportReport'])->name('export');
                Route::get('/{id}', [PenggajianController::class, 'adminShow'])->name('show');
                Route::get('/{id}/edit', [PenggajianController::class, 'adminEdit'])->name('edit');
                Route::put('/{id}', [PenggajianController::class, 'adminUpdate'])->name('update');
                Route::delete('/{id}', [PenggajianController::class, 'adminDestroy'])->name('destroy');
                Route::put('/{id}/status', [PenggajianController::class, 'adminUpdateStatus'])->name('update-status');
                Route::get('/{id}/send-payslip', [PenggajianController::class, 'sendPayslip'])->name('send-payslip');
                Route::get('/{id}/download', [PenggajianController::class, 'downloadPayslip'])->name('download');
                Route::post('/bulk-whatsapp', [PenggajianController::class, 'bulkSendWhatsapp'])->name('bulk-whatsapp');
            });

        // Change Day
        Route::get('change-day', [ChangedayController::class, 'index'])->name('changeday.index');

        // Pengumuman Management for Admin/HR
        Route::prefix('pengumuman')
            ->name('pengumuman.')
            ->group(function () {
                Route::get('/', [PengumumanController::class, 'index'])->name('index');
                Route::get('/create', [PengumumanController::class, 'create'])->name('create');
                Route::post('/', [PengumumanController::class, 'store'])->name('store');
                Route::get('/{id}', [PengumumanController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [PengumumanController::class, 'edit'])->name('edit');
                Route::put('/{id}', [PengumumanController::class, 'update'])->name('update');
                Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
            });

        // Performa Management
        Route::prefix('performa')
            ->name('performa.')
            ->group(function () {
                Route::get('/', [PerformaController::class, 'adminIndex'])->name('index');
                Route::get('/create', [PerformaController::class, 'adminCreate'])->name('create');
                Route::get('/bulk', [PerformaController::class, 'adminBulkCreate'])->name('bulk');
                Route::post('/bulk', [PerformaController::class, 'adminBulkStore'])->name('bulk.store');
                Route::get('/karyawan/{id}', [PerformaController::class, 'getKaryawanData'])->name('get-karyawan');
                Route::get('/attendance-rate', [PerformaController::class, 'getAttendanceRate'])->name('attendance-rate');
                Route::get('/sync-tracker', [PerformaController::class, 'syncFromTracker'])->name('sync-tracker');
                Route::get('/sync-tracker/{karyawanId}', [PerformaController::class, 'syncFromTrackerSingle'])->name('sync-tracker-single');
                Route::post('/', [PerformaController::class, 'adminStore'])->name('store');
                Route::get('/{id}/edit', [PerformaController::class, 'adminEdit'])->name('edit');
                Route::put('/{id}', [PerformaController::class, 'adminUpdate'])->name('update');
                Route::delete('/{id}', [PerformaController::class, 'adminDestroy'])->name('destroy');
                Route::get('/{id}', [PerformaController::class, 'adminShow'])->name('show');
                Route::post('/check-reset', [PerformaController::class, 'checkAndResetKPI'])->name('check-reset');
            });
    });

// Employee Routes
Route::middleware(['auth', 'karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');
    });

// Break
Route::middleware('auth')->group(function () {
    Route::post('/break/{id}/start', [BreakController::class, 'start'])->name('break.start');
    Route::post('/break/{id}/end', [BreakController::class, 'end'])->name('break.end');
    Route::get('/attendance/status', [DashboardController::class, 'getStatus'])->name('attendance.status');
});

require __DIR__ . '/auth.php';
