<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Major;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Tampilkan halaman Master Data Kelas dan Jurusan.
     */
    public function index()
    {
        $classes = SchoolClass::orderBy('name')->get();
        $majors  = Major::orderBy('name')->get();

        return view('admin.master-data.index', compact('classes', 'majors'));
    }
}
