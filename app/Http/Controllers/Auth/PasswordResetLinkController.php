<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan']);
        }

        // ADMIN → default Laravel reset link
        if ($user->hasRole('admin')) {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status == Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
        }

        // NON-ADMIN (member/user) → kirim OTP
        $otpController = new PasswordOtpController();
        $result = $otpController->sendOtp($user);

        // Jika sendOtp mengembalikan redirect (rate limit), teruskan
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return $result;
        }

        // Simpan user_id di session agar form OTP tahu OTP milik siapa
        session(['otp_pending_user_id' => $user->id]);

        return redirect()->route('password.otp.form')
            ->with('status', 'Kode OTP telah dikirim ke email kamu.');
    }
}
