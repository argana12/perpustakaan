<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberApprovalController extends Controller
{
    /**
     * Tampilkan antrian member yang menunggu persetujuan.
     */
    public function index()
    {
        $pendingMembers = User::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $classes = SchoolClass::orderBy('name')->get();
        $majors  = Major::orderBy('name')->get();

        return view('petugas.member-approval', compact('pendingMembers', 'classes', 'majors'));
    }

    /**
     * Generate kode aktivasi untuk satu user.
     * Untuk role student → wajib isi nama, kelas, jurusan.
     */
    public function generateCode(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:student,teacher']);

        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if ($user->status !== 'pending') {
            abort(403, 'User sudah diproses');
        }

        if (ActivationCode::where('user_id', $user->id)->exists()) {
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
        while (ActivationCode::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(4)) . rand(100, 999);
        }

        ActivationCode::create([
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
     * Tampilkan semua user (member, suspended) — untuk monitoring petugas.
     */
    public function allUsers()
    {
        $users = User::role('member')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['approved', 'suspended']);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('petugas.all-users', compact('users'));
    }

    /**
     * Unlock Suspended User (hanya untuk student/teacher jika petugas)
     */
    public function unlock(User $user)
    {
        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if ($user->hasRole(['admin', 'petugas'])) {
            return back()->withErrors(['error' => 'Anda tidak memiliki hak untuk membuka blokir role ini.']);
        }

        if ($user->status !== 'suspended') {
            return back()->withErrors(['error' => 'User ini tidak sedang disuspend.']);
        }

        $user->update([
            'status'       => 'approved',
            'code_attempt' => 0,
        ]);

        return back()->with('status', "Akun " . $user->name . " berhasil di-unlock dan dapat mencoba input kode kembali.");
    }

    /**
     * Hapus member secara permanen (hanya untuk student/teacher)
     */
    public function destroyUser(User $user)
    {
        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if ($user->hasRole(['admin', 'petugas'])) {
            return back()->withErrors(['error' => 'Petugas hanya boleh menghapus akun member (murid/guru).']);
        }

        $nama = $user->name;
        $user->delete();

        return back()->with('status', "Akun member {$nama} berhasil dihapus permanen.");
    }
}
