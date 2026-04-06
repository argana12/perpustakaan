<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StaffScheduleController extends Controller
{
    /**
     * Tampilkan halaman jadwal petugas.
     */
    public function index()
    {
        $petugasList = User::role('petugas')->orderBy('name')->get();
        
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.staff-schedule', compact('petugasList', 'hariList'));
    }

    /**
     * Update jadwal hari jaga petugas.
     */
    public function update(Request $request, User $user)
    {
        if (!$user->hasRole('petugas')) {
            abort(403, 'Aksi khusus untuk petugas.');
        }

        $request->validate([
            'work_days'  => 'nullable|array',
            'is_visible' => 'nullable|boolean'
        ]);

        $days = $request->input('work_days', []);
        
        // Simpan sebagai comma-separated string
        $workDaysString = !empty($days) ? implode(', ', $days) : null;
        
        // is_visible = aktif? Default ke false jika checkbox tidak dicentang
        $isVisible = $request->boolean('is_visible');

        $user->update([
            'work_days'  => $workDaysString,
            'is_visible' => $isVisible,
        ]);

        return back()->with('status', "Jadwal petugas {$user->name} berhasil diperbarui.");
    }
}
