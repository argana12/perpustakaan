<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use App\Models\LabelColor;
use App\Models\Rack;
use Illuminate\Http\Request;

class LabelMasterController extends Controller
{
    public function index()
    {
        $colors = LabelColor::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();
        $categories = BookCategory::orderBy('name')->get();

        return view('admin.label-master.index', compact('colors', 'racks', 'categories'));
    }

    public function storeColor(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:label_colors,name'],
            'hex' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        LabelColor::create($validated);
        return back()->with('success', 'Warna label ditambahkan.');
    }

    public function destroyColor(LabelColor $labelColor)
    {
        $labelColor->delete();
        return back()->with('success', 'Warna label dihapus.');
    }

    public function storeRack(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:racks,code'],
            'name' => ['nullable', 'string', 'max:100'],
        ]);

        Rack::create($validated);
        return back()->with('success', 'Rak ditambahkan.');
    }

    public function destroyRack(Rack $rack)
    {
        $rack->delete();
        return back()->with('success', 'Rak dihapus.');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:book_categories,name'],
            'label_color' => ['nullable', 'string', 'max:30', 'exists:label_colors,name'],
        ]);

        BookCategory::create($validated);
        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function destroyCategory(BookCategory $bookCategory)
    {
        $bookCategory->delete();
        return back()->with('success', 'Kategori dihapus.');
    }
}
