<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordOtp;
use App\Models\SchoolClass;
use App\Models\Major;
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
            ->where(function ($query) {
                $query->where('otp_attempt', '>', 0)
                      ->orWhere('status', 'suspended')
                      ->orWhereHas('passwordOtps', function ($q) {
                          $q->where('attempt', '>', 0);
                      });
            })
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

        $classes = SchoolClass::orderBy('name')->get();
        $majors  = Major::orderBy('name')->get();

        return view('admin.pending-users', compact('pendingUsers', 'classes', 'majors'));
    }

    /**
     * Generate kode aktivasi untuk user (khusus admin).
     * Untuk role student → wajib isi nama, kelas, jurusan terlebih dahulu.
     */
    public function generateCode(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:student,teacher,petugas']);

        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        if ($user->status !== 'pending') {
            abort(403, 'User sudah diproses');
        }

        if (\App\Models\ActivationCode::where('user_id', $user->id)->exists()) {
            return back()->withErrors(['error' => 'User sudah memiliki kode aktivasi yang aktif.']);
        }

        // Jika role adalah student → wajib ada kelas & jurusan
        if ($request->role === 'student') {
            $request->validate([
                'name'    => 'required|string|max:255',
                'kelas'   => 'required|string|max:50',
                'jurusan' => 'required|string|max:100',
            ], [
                'name.required'    => 'Nama lengkap harus diisi.',
                'kelas.required'   => 'Kelas harus dipilih.',
                'jurusan.required' => 'Jurusan harus dipilih.',
            ]);

            // Perbarui data user
            $user->update([
                'name'    => trim($request->name),
                'kelas'   => $request->kelas,
                'jurusan' => $request->jurusan,
            ]);
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

        $user->update(['status' => 'approved']);

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
