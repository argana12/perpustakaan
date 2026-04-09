<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth page.
     */
    public function redirect(Request $request)
    {
        return Socialite::driver('google')
            ->redirectUrl($this->resolveRedirectUrl($request))
            ->redirect();
    }

    /**
     * Handle incoming callback from Google.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->resolveRedirectUrl($request))
                ->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update credentials if they didn't have google_id
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                return view('auth.register', [
                    'google_email' => $googleUser->getEmail(),
                    'google_id'    => $googleUser->getId(),
                ]);
            }

            // Redirect Handlers depending on Account Status
            if ($user->status === 'active') {
                Auth::login($user);
                return redirect()->intended(route('dashboard'));
            } elseif (in_array($user->status, ['pending', 'approved'])) {
                // Redirect exactly like how Email OTP success behaves
                session(['verify_user_id' => $user->id]);
                return redirect()->route('pending.approval')
                    ->with('status', 'Login via Google berhasil. Akun Anda sedang menunggu atau mendapat persetujuan petugas.');
            } elseif ($user->status === 'suspended') {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda disuspend. Silakan hubungi petugas/admin.']);
            } elseif ($user->status === 'rejected') {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Pendaftaran akun Anda ditolak.']);
            } else {
                // Fallback (e.g. 'register')
                session(['verify_user_id' => $user->id]);
                return redirect()->route('pending.approval');
            }

        } catch (Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal terhubung dengan Google. Silakan ulangi kembali.']);
        }
    }

    private function resolveRedirectUrl(Request $request): string
    {
        $configured = trim((string) config('services.google.redirect', ''));
        if ($configured !== '') {
            return $configured;
        }

        return $request->getSchemeAndHttpHost() . '/auth/google/callback';
    }

    /**
     * Submit google registration with custom name.
     */
    public function completeRegistration(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'google_id' => 'required',
        ]);

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'google_id'          => $request->google_id,
            'password'           => bcrypt(str()->random(24)),
            'status'             => 'pending',
            'is_verified'        => true,
            'pending_expired_at' => now()->addHours(24),
        ]);

        session(['verify_user_id' => $user->id]);
        return redirect()->route('pending.approval')
            ->with('status', 'Pendaftaran via Google berhasil. Akun Anda sedang menunggu persetujuan petugas.');
    }
}
