<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserOtpController extends Controller
{
    /**
     * Tampilkan halaman manajemen OTP semua user.
     */
    public function index()
    {
        $users = User::whereNot('name', 'admin')
            ->orderBy('name')
            ->get();

        return view('admin.otp-management', compact('users'));
    }

    /**
     * Reset cooldown OTP user — set otp_unlocked = true.
     */
    public function unlock(User $user)
    {
        $user->update([
            'otp_unlocked' => true,
            'code_attempt' => 0,
        ]);

        if ($user->status === 'suspended') {
            $user->update(['status' => 'approved']);
            return back()->with('status', "Status suspen user [{$user->name}] berhasil dibuka. User bisa mencoba masukkan kode lagi.");
        }

        return back()->with('status', "Cooldown OTP user [{$user->name}] berhasil direset. User bisa kirim OTP lagi.");
    }

    /**
     * Reset SEMUA data OTP user (attempt, cooldown, hapus dari tabel).
     */
    public function resetFull(User $user)
    {
        PasswordOtp::where('user_id', $user->id)->delete();

        $user->update([
            'otp'                 => null,
            'otp_expired_at'      => null,
            'otp_attempt'         => 0,
            'otp_next_allowed_at' => null,
            'otp_unlocked'        => false,
        ]);

        return back()->with('status', "Data OTP user [{$user->name}] berhasil direset penuh.");
    }

    // =========================================================
    // =========================================================
    // APPROVAL USER (ADMIN VIEW)
    // =========================================================

    /**
     * Tampilkan daftar user yang menunggu persetujuan.
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.pending-users', compact('pendingUsers'));
    }

    /**
     * Generate kode aktivasi untuk user (Bisa semua role).
     */
    public function generateCode(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:student,teacher,petugas']);

        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        if ($user->status !== 'pending') {
            return back()->withErrors(['error' => 'User ini tidak dalam antrian persetujuan.']);
        }

        if (\App\Models\ActivationCode::where('user_id', $user->id)->exists()) {
            return back()->withErrors(['error' => 'User sudah memiliki kode aktivasi yang aktif.']);
        }

        // Generate kode unik
        $code = strtoupper(Str::random(4)) . rand(100, 999);
        while (\App\Models\ActivationCode::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(4)) . rand(100, 999);
        }

        \App\Models\ActivationCode::create([
            'code'       => $code,
            'user_id'    => $user->id,
            'role'       => $request->role,
            'created_by' => auth()->id(),
            'expired_at' => now()->addHours(24),
        ]);

        $user->update([
            'status' => 'approved' 
        ]);

        return back()->with('generated', [
            'user_id' => $user->id,
            'nama'    => $user->name,
            'email'   => $user->email,
            'kode'    => $code,
            'role'    => $request->role,
        ]);
    }

    /**
     * Tolak pendaftaran.
     */
    public function rejectUser(User $user)
    {
        $user->update(['status' => 'rejected']);
        return back()->with('status', "Pendaftaran [{$user->name}] ditolak.");
    }

    /**
     * Hapus user secara permanen
     */
    public function destroyUser(User $user)
    {
        if ($user->hasRole('admin')) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun Admin.']);
        }

        $nama = $user->name;
        $user->delete();

        return back()->with('status', "Akun {$nama} berhasil dihapus permanen.");
    }
}
