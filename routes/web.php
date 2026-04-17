<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DashboardController;
// use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\OfficeWorkTimeController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users-admin', [UsersController::class, 'index'])->name('users.index');
    Route::post('/users', [UsersController::class, 'store'])->name('users.add');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    Route::get('/office', [OfficeController::class, 'edit'])->name('office.edit');
    Route::put('/office', [OfficeController::class, 'update'])->name('office.update');

    Route::get('/worktime', [OfficeWorkTimeController::class, 'edit'])->name('office.worktime.edit');
    Route::put('/worktime', [OfficeWorkTimeController::class, 'update'])->name('office.worktime.update');

    

    Route::get('/get-face-data/{user_id}', [AttendanceController::class, 'getFaceData']);

    Route::get('/scan', function () {
        $office = \App\Models\Office::first(); // Ambil office default
        return view('admin.attendance.scan', compact('office'));
    })->name('attendance.scan');


    // Halaman scan absensi
    // Route::get('/scan', function () {
    //     $office = \App\Models\Office::first(); // Ambil office default
    //     return view('admin.attendance.scan', compact('office'));
    // })->name('attendance.scan');

    // Face register
    Route::post('/face-register', [AttendanceController::class, 'storeFace'])
        ->name('attendance.face-register');

    // Check In
    Route::post('/check-in', [AttendanceController::class, 'checkIn'])
        ->name('attendance.check-in');

    // Check Out
    Route::post('/check-out', [AttendanceController::class, 'checkOut'])
        ->name('attendance.check-out');

    // Absensi hari ini
    Route::get('/attendance-today/{user_id}', [AttendanceController::class, 'todayAttendance'])
        ->name('attendance.today');

    // Halaman data absensi
    Route::get('/attendance', [AttendanceController::class, 'indexAbsensidata'])->name('attendance.index');

    // Hapus absensi
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    // Export absensi
    Route::get('/attendance/export', [AttendanceController::class, 'export'])
    ->name('attendance.export')
    ->middleware('auth');
});

require __DIR__.'/auth.php';
