<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Mail\SendOtpMail;

class PasswordOtpController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        // ❗ ADMIN TIDAK BOLEH OTP
        if ($user->hasRole('admin')) {
            return back()->withErrors(['email' => 'Admin tidak pakai OTP']);
        }

        $otp = rand(100000, 999999);

        DB::table('password_otps')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(5),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        Mail::to($user->email)->send(new SendOtpMail($otp));

        session(['otp_user' => $user->id]);

        return redirect()->route('otp.form');
    }

    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        $record = DB::table('password_otps')
            ->where('user_id', session('otp_user'))
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'OTP tidak ditemukan']);
        }

        if ($record->otp != $request->otp) {
            return back()->withErrors(['otp' => 'OTP salah']);
        }

        if (Carbon::now()->gt($record->expired_at)) {
            return back()->withErrors(['otp' => 'OTP expired']);
        }

        return redirect()->route('password.new');
    }

    public function showNewPassword()
    {
        return view('auth.new-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::find(session('otp_user'));

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_otps')->where('user_id', $user->id)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diubah');
    }
}