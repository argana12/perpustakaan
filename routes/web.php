<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserOtpController;
use App\Http\Controllers\Petugas\MemberApprovalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Manajemen OTP User
    Route::get('/otp-management', [UserOtpController::class, 'index'])->name('otp.index');
    Route::post('/otp-management/{user}/unlock', [UserOtpController::class, 'unlock'])->name('otp.unlock');
    Route::post('/otp-management/{user}/reset', [UserOtpController::class, 'resetFull'])->name('otp.reset');

    // Jadwal Petugas
    Route::get('/staff-schedule', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'index'])->name('staff.schedule.index');
    Route::put('/staff-schedule/{user}', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'update'])->name('staff.schedule.update');

    // Persetujuan All Users (CRM)
    Route::get('/pending-users', [UserOtpController::class, 'pendingUsers'])->name('pending.users');
    Route::post('/pending-users/{user}/generate-code', [UserOtpController::class, 'generateCode'])->name('users.generate.code');
    Route::post('/pending-users/{user}/reject', [UserOtpController::class, 'rejectUser'])->name('users.reject');
    Route::delete('/users/{user}', [UserOtpController::class, 'destroyUser'])->name('users.destroy');
});

/*
|--------------------------------------------------------------------------
| PETUGAS Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {

    Route::get('/', function () {
        return view('petugas.dashboard');
    })->name('dashboard');

    // Approval Member
    Route::get('/member-approval', [MemberApprovalController::class, 'index'])->name('member.approval');
    Route::post('/member-approval/{user}/generate-code', [MemberApprovalController::class, 'generateCode'])->name('member.generate.code');
    Route::post('/member-approval/{user}/unlock', [MemberApprovalController::class, 'unlock'])->name('member.unlock');

    // Lihat semua user
    Route::get('/users', [MemberApprovalController::class, 'allUsers'])->name('users');
    Route::delete('/members/{user}', [MemberApprovalController::class, 'destroyUser'])->name('member.destroy');
});

/*
|--------------------------------------------------------------------------
| MEMBER / General Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'active'])->name('dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
