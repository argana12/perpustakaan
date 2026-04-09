<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Major;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan halaman seluruh user dengan filter.
     */
    public function index(Request $request)
    {
        $query = User::query()->whereNot('name', 'admin');

        // Filter Pencarian Nama / Email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter Jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        $classes = SchoolClass::orderBy('name')->get();
        $majors  = Major::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'classes', 'majors'));
    }
}
