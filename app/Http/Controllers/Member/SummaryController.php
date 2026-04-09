<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Summary;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function store(Request $request, Borrow $borrow)
    {
        if ($borrow->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => ['required', 'image', 'max:3072'],
        ]);

        $path = $request->file('file')->store('summaries', 'public');

        Summary::updateOrCreate(
            ['borrow_id' => $borrow->id],
            ['file' => $path, 'status' => 'pending', 'review_note' => null]
        );

        return back()->with('success', 'Rangkuman berhasil diupload.');
    }
}
