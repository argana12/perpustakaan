<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:school_classes,name',
        ], [
            'name.required' => 'Nama kelas tidak boleh kosong.',
            'name.unique'   => 'Kelas ini sudah ada.',
        ]);

        SchoolClass::create(['name' => strtoupper(trim($request->name))]);

        return back()->with('class_added', 'Kelas "' . strtoupper(trim($request->name)) . '" berhasil ditambahkan.');
    }

    public function destroy(SchoolClass $schoolClass)
    {
        $schoolClass->delete();
        return back()->with('class_deleted', 'Kelas berhasil dihapus.');
    }
}
