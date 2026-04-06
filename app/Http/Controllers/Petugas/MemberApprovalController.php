<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        return view('petugas.member-approval', compact('pendingMembers'));
    }

    /**
     * Generate kode aktivasi untuk satu user.
     */
    public function generateCode(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:student,teacher']);

        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if ($user->status !== 'pending') {
            return back()->withErrors(['error' => 'User ini tidak dalam status pending.']);
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
            'status' => 'approved' // now they can input the code
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
     * Tampilkan semua user (member, suspended) — untuk monitoring petugas.
     */
    public function allUsers()
    {
        // View also suspended members
        $users = User::role('member')
            ->orWhere(function ($q) {
                // Suspended user that doesn't have a role yet?
                // Actually they don't have a role if they failed at 'approved' stage!
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

        // Petugas cannot unlock admin/petugas
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
