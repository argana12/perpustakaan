<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:majors,name',
        ], [
            'name.required' => 'Nama jurusan tidak boleh kosong.',
            'name.unique'   => 'Jurusan ini sudah ada.',
        ]);

        Major::create(['name' => trim($request->name)]);

        return back()->with('major_added', 'Jurusan "' . trim($request->name) . '" berhasil ditambahkan.');
    }

    public function destroy(Major $major)
    {
        $major->delete();
        return back()->with('major_deleted', 'Jurusan berhasil dihapus.');
    }
}
