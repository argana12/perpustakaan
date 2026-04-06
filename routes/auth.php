<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordOtpController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes — hanya bisa diakses sebelum login
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Register
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Google OAuth
    Route::get('auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('google.login');
    Route::get('auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback']);
    Route::post('auth/google/register', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'completeRegistration']);

    /*
    |--------------------------------------------------------------------------
    | Lupa Sandi (Forgot Password)
    |--------------------------------------------------------------------------
    */
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    /*
    |--------------------------------------------------------------------------
    | OTP Lupa Sandi (untuk Non-Admin)
    |--------------------------------------------------------------------------
    */
    Route::get('verify-otp', [PasswordOtpController::class, 'showForm'])->name('password.otp.form');
    Route::post('verify-otp', [PasswordOtpController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('resend-otp', [PasswordOtpController::class, 'resendOtp'])->name('password.otp.resend');
    Route::get('reset-password-otp', [PasswordOtpController::class, 'showResetForm'])->name('password.otp.reset');
    Route::post('update-password-otp', [PasswordOtpController::class, 'resetPassword'])->name('password.update.otp');

    /*
    |--------------------------------------------------------------------------
    | OTP Register
    |--------------------------------------------------------------------------
    */
    Route::get('verify-register-otp', [PasswordOtpController::class, 'showRegisterOtp'])->name('register.otp.form');
    Route::post('verify-register-otp', [PasswordOtpController::class, 'verifyRegisterOtp'])->name('register.otp.verify');
    Route::post('resend-register-otp', [PasswordOtpController::class, 'resendRegisterOtp'])->name('register.otp.resend');

    /*
    |--------------------------------------------------------------------------
    | Pending Approval — Halaman menunggu (boleh diakses tanpa login)
    |--------------------------------------------------------------------------
    */
    Route::get('pending-approval', [PasswordOtpController::class, 'showPendingApproval'])->name('pending.approval');

    /*
    |--------------------------------------------------------------------------
    | Input Kode Aktivasi (setelah petugas generate)
    |--------------------------------------------------------------------------
    */

    Route::post('input-code', [PasswordOtpController::class, 'verifyCode'])->name('registration.code.verify');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});