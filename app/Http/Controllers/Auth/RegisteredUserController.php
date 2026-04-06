<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'password'            => Hash::make($request->password),
            'member_type'         => null,
            'status'              => 'register',
            'otp'                 => $otp,
            'otp_expired_at'      => now()->addMinutes(5),
            'otp_attempt'         => 1,
            'otp_next_allowed_at' => now()->addMinutes(5),
            'is_verified'         => false,
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp, 'register'));

        session()->put('user_id', $user->id);

        return redirect()->route('register.otp.form')
            ->with('status', 'Kode OTP telah dikirim ke email kamu.');
    }
}
