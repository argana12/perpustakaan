<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookQueue;
use App\Models\Borrow;
use App\Models\Summary;
use App\Models\User;
use App\Services\BookQueueManager;
use App\Services\BookQueueNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    public function loanMode(Request $request)
    {
        $this->markOverdueBorrows();

        $book = null;
        $q = trim((string) $request->get('code', ''));
        if ($q !== '') {
            $book = Book::where('code', $q)->first();
        }

        $students = collect();
        $studentQ = trim((string) $request->get('student', ''));
        if ($studentQ !== '') {
            $students = User::query()
                ->role('member')
                ->where('status', 'active')
                ->where('name', 'like', "%{$studentQ}%")
                ->limit(10)
                ->get()
                ->map(function (User $student) {
                    $activeBorrowCount = Borrow::where('user_id', $student->id)
                        ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
                        ->count();

                    $maxBorrow = $student->member_type === 'teacher' ? 3 : 2;
                    $hasUnpaidFine = Borrow::where('user_id', $student->id)
                        ->where('status', Borrow::STATUS_RETURNED)
                        ->where('fine', '>', 0)
                        ->whereNull('fine_paid_at')
                        ->exists();

                    $student->can_borrow = !$hasUnpaidFine && $activeBorrowCount < $maxBorrow;
                    $student->borrow_note = $hasUnpaidFine
                        ? 'Masih punya denda belum lunas'
                        : ($activeBorrowCount >= $maxBorrow ? "Limit {$maxBorrow} buku tercapai" : 'Siap pinjam');

                    return $student;
                });
        }

        return view('petugas.circulation.loan', compact('book', 'q', 'students', 'studentQ'));
    }

    public function processLoan(Request $request)
    {
        $validated = $request->validate([
            'book_code' => ['required', 'string', 'exists:books,code'],
            'user_id' => ['required', 'exists:users,id'],
            'duration_days' => ['required', 'integer', 'in:4,6,7,12,15,16'],
        ]);

        $book = Book::where('code', $validated['book_code'])->firstOrFail();
        $user = User::findOrFail($validated['user_id']);

        if (!$user->hasRole('member')) {
            return back()->withErrors(['user_id' => 'Peminjam harus role member.'])->withInput();
        }

        if (!in_array($book->status, [Book::STATUS_AVAILABLE], true)) {
            return back()->withErrors(['book_code' => 'Buku tidak tersedia untuk dipinjam.'])->withInput();
        }

        $activeBorrowCount = Borrow::where('user_id', $user->id)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->count();

        $maxBorrow = $user->member_type === 'teacher' ? 3 : 2;
        if ($activeBorrowCount >= $maxBorrow) {
            return back()->withErrors(['user_id' => "Batas pinjam {$user->name} sudah mencapai {$maxBorrow} buku."])->withInput();
        }

        $hasUnpaidFine = Borrow::where('user_id', $user->id)
            ->where('status', Borrow::STATUS_RETURNED)
            ->where('fine', '>', 0)
            ->whereNull('fine_paid_at')
            ->exists();

        if ($hasUnpaidFine) {
            return back()->withErrors(['user_id' => 'User masih memiliki denda, tidak bisa pinjam dulu.'])->withInput();
        }

        DB::transaction(function () use ($book, $user, $validated): void {
            $now = now();
            Borrow::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrow_date' => $now,
                'due_date' => $now->copy()->addDays((int) $validated['duration_days']),
                'status' => Borrow::STATUS_ACTIVE,
                'fine' => 0,
            ]);

            $book->update(['status' => Book::STATUS_BORROWED]);
        });

        return redirect()->route('petugas.circulation.loan', ['code' => $book->code])
            ->with('success', 'Peminjaman berhasil dikonfirmasi.');
    }

    public function scannedBookData(string $code)
    {
        $book = Book::where('code', strtoupper(trim($code)))->first();
        if (!$book) {
            return response()->json([
                'ok' => false,
                'message' => 'Buku tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'book' => [
                'id' => $book->id,
                'code' => $book->code,
                'title' => $book->title,
                'author' => $book->author,
                'status' => strtoupper($book->status),
                'pages' => $book->pages,
                'category' => $book->category,
                'rack_code' => $book->rack_code,
                'cover_url' => $book->cover_image ? route('books.cover', $book) : null,
            ],
        ]);
    }

    public function scannedReturnData(string $code)
    {
        $book = Book::where('code', strtoupper(trim($code)))->first();
        if (!$book) {
            return response()->json([
                'ok' => false,
                'message' => 'Buku tidak ditemukan.',
            ], 404);
        }

        $activeBorrow = Borrow::with(['user', 'summary'])
            ->where('book_id', $book->id)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->latest('borrow_date')
            ->first();

        $borrowPayload = null;
        if ($activeBorrow) {
            $now = now();
            $isLate = $now->gt($activeBorrow->due_date);
            $lateDays = $isLate
                ? $activeBorrow->due_date->copy()->startOfDay()->diffInDays($now->copy()->startOfDay())
                : 0;

            $borrowPayload = [
                'id' => $activeBorrow->id,
                'status' => strtoupper($activeBorrow->status),
                'borrow_date' => optional($activeBorrow->borrow_date)->format('d-m-Y H:i'),
                'due_date' => optional($activeBorrow->due_date)->format('d-m-Y H:i'),
                'time_status' => $isLate ? 'LATE' : 'ON TIME',
                'late_days' => $lateDays,
                'fine_preview' => $lateDays * 5000,
                'borrower' => [
                    'id' => $activeBorrow->user?->id,
                    'name' => $activeBorrow->user?->name,
                    'kelas' => $activeBorrow->user?->kelas,
                    'jurusan' => $activeBorrow->user?->jurusan,
                ],
                'summary' => [
                    'uploaded' => (bool) $activeBorrow->summary,
                    'status' => $activeBorrow->summary?->status,
                ],
            ];
        }

        return response()->json([
            'ok' => true,
            'book' => [
                'id' => $book->id,
                'code' => $book->code,
                'title' => $book->title,
                'author' => $book->author,
                'status' => strtoupper($book->status),
                'pages' => $book->pages,
                'category' => $book->category,
                'rack_code' => $book->rack_code,
                'cover_url' => $book->cover_image ? route('books.cover', $book) : null,
            ],
            'borrow' => $borrowPayload,
        ]);
    }

    public function searchBorrower(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') {
            return response()->json(['ok' => true, 'items' => []]);
        }

        $items = User::query()
            ->role('member')
            ->where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
                if (ctype_digit($q)) {
                    $query->orWhere('id', (int) $q);
                }
            })
            ->limit(10)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'kelas' => $user->kelas,
                'jurusan' => $user->jurusan,
                'member_type' => $user->member_type,
            ])
            ->values();

        return response()->json([
            'ok' => true,
            'items' => $items,
        ]);
    }

    public function borrowerSummary(User $user)
    {
        $activeBorrowCount = Borrow::where('user_id', $user->id)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->count();

        $maxBorrow = $user->member_type === 'teacher' ? 3 : 2;
        $remainingBorrow = max(0, $maxBorrow - $activeBorrowCount);

        $unpaidFine = Borrow::where('user_id', $user->id)
            ->where('status', Borrow::STATUS_RETURNED)
            ->where('fine', '>', 0)
            ->whereNull('fine_paid_at')
            ->sum('fine');

        $lateHistoryCount = Borrow::where('user_id', $user->id)
            ->where('fine', '>', 0)
            ->count();

        $lostHistoryCount = Borrow::where('user_id', $user->id)
            ->where('status', Borrow::STATUS_LOST)
            ->count();

        $recentHistory = Borrow::with('book')
            ->where('user_id', $user->id)
            ->latest('borrow_date')
            ->limit(5)
            ->get()
            ->map(function (Borrow $borrow) {
                return [
                    'book' => $borrow->book?->title,
                    'status' => strtoupper($borrow->status),
                    'borrow_date' => optional($borrow->borrow_date)->format('d-m-Y H:i'),
                    'return_date' => optional($borrow->return_date)->format('d-m-Y H:i'),
                    'fine' => (int) $borrow->fine,
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'kelas' => $user->kelas,
                'jurusan' => $user->jurusan,
                'member_type' => $user->member_type,
            ],
            'borrow' => [
                'active_count' => $activeBorrowCount,
                'max' => $maxBorrow,
                'remaining' => $remainingBorrow,
            ],
            'risk' => [
                'unpaid_fine' => (int) $unpaidFine,
                'late_history_count' => $lateHistoryCount,
                'lost_history_count' => $lostHistoryCount,
            ],
            'recent_history' => $recentHistory,
        ]);
    }

    public function returnMode(Request $request)
    {
        $this->markOverdueBorrows();

        $book = null;
        $borrow = null;
        $q = trim((string) $request->get('code', ''));
        if ($q !== '') {
            $book = Book::where('code', $q)->first();
            if ($book) {
                $borrow = Borrow::with(['user', 'summary'])
                    ->where('book_id', $book->id)
                    ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
                    ->latest('borrow_date')
                    ->first();
            }
        }

        $readyQueues = BookQueue::query()
            ->with(['book', 'user'])
            ->where('status', BookQueue::STATUS_READY)
            ->orderBy('ready_at')
            ->orderBy('created_at')
            ->limit(15)
            ->get();

        $calledQueues = BookQueue::query()
            ->with(['book', 'user'])
            ->where('status', BookQueue::STATUS_CALLED)
            ->orderBy('deadline')
            ->limit(15)
            ->get();

        return view('petugas.circulation.return', compact('book', 'borrow', 'q', 'readyQueues', 'calledQueues'));
    }

    public function processReturn(Request $request, BookQueueManager $queueManager)
    {
        $validated = $request->validate([
            'book_code' => ['required', 'string', 'exists:books,code'],
        ]);

        $book = Book::where('code', $validated['book_code'])->firstOrFail();
        $borrow = Borrow::where('book_id', $book->id)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->latest('borrow_date')
            ->first();

        if (!$borrow) {
            return back()->withErrors(['book_code' => 'Tidak ada transaksi aktif untuk buku ini.'])->withInput();
        }

        $now = now();
        $lateDays = max(0, Carbon::parse($borrow->due_date)->startOfDay()->diffInDays($now->copy()->startOfDay(), false));
        $fine = $lateDays > 0 ? $lateDays * 5000 : 0;

        DB::transaction(function () use ($borrow, $book, $now, $fine, $queueManager): void {
            $borrow->update([
                'return_date' => $now,
                'status' => Borrow::STATUS_RETURNED,
                'fine' => $fine,
            ]);

            $book->update(['status' => Book::STATUS_AVAILABLE]);

            // Setelah buku kembali, antrean berikutnya disiapkan terlebih dahulu (READY),
            // belum dipanggil otomatis agar petugas tetap punya kontrol manual.
            $queueManager->markNextQueueReady($book, $now);
        });

        return redirect()->route('petugas.circulation.return', ['code' => $book->code])
            ->with('success', 'Buku berhasil dikembalikan.');
    }

    public function callQueue(BookQueue $bookQueue, BookQueueManager $queueManager)
    {
        if ($bookQueue->status !== BookQueue::STATUS_READY) {
            return back()->withErrors(['queue' => 'Antrian tidak dalam status READY.']);
        }

        $queueManager->callQueue($bookQueue);

        return back()->with('success', 'Antrian berhasil dipanggil. Lanjutkan kirim notifikasi jika diperlukan.');
    }

    public function notifyQueue(BookQueue $bookQueue, BookQueueNotificationService $notifier)
    {
        if ($bookQueue->status !== BookQueue::STATUS_CALLED) {
            return back()->withErrors(['queue' => 'Notifikasi hanya bisa dikirim untuk status CALLED.']);
        }

        $notifier->notifyCalled($bookQueue, 'manual');

        return back()->with('success', 'Notifikasi WA + Email berhasil diproses.');
    }

    public function completeQueue(BookQueue $bookQueue)
    {
        if (!in_array($bookQueue->status, [BookQueue::STATUS_CALLED, BookQueue::STATUS_READY], true)) {
            return back()->withErrors(['queue' => 'Status antrian tidak bisa ditandai selesai.']);
        }

        $bookQueue->update(['status' => BookQueue::STATUS_DONE]);

        return back()->with('success', 'Antrian ditandai selesai.');
    }

    public function showBookDetail(string $code)
    {
        $this->markOverdueBorrows();

        $book = Book::where('code', $code)->firstOrFail();
        $history = Borrow::with(['user', 'summary'])
            ->where('book_id', $book->id)
            ->latest('borrow_date')
            ->paginate(10);

        $availableCount = Book::where('status', Book::STATUS_AVAILABLE)->count();

        return view('petugas.circulation.book-detail', compact('book', 'history', 'availableCount'));
    }

    public function payFine(Borrow $borrow)
    {
        if ($borrow->fine <= 0) {
            return back()->with('success', 'Transaksi ini tidak memiliki denda.');
        }

        if ($borrow->fine_paid_at) {
            return back()->with('success', 'Denda sudah dilunasi sebelumnya.');
        }

        $borrow->update([
            'fine_paid_at' => now(),
        ]);

        return back()->with('success', 'Denda berhasil dilunasi.');
    }

    public function approveSummary(Summary $summary)
    {
        $summary->update(['status' => 'approved', 'review_note' => null]);

        return back()->with('success', 'Rangkuman berhasil disetujui.');
    }

    public function rejectSummary(Request $request, Summary $summary)
    {
        $validated = $request->validate([
            'review_note' => ['required', 'string', 'max:255'],
        ]);

        $summary->update([
            'status' => 'rejected',
            'review_note' => $validated['review_note'],
        ]);

        return back()->with('success', 'Rangkuman ditolak.');
    }

    public function fines()
    {
        $fines = Borrow::with(['user', 'book'])
            ->where('status', Borrow::STATUS_RETURNED)
            ->where('fine', '>', 0)
            ->whereNull('fine_paid_at')
            ->latest('return_date')
            ->paginate(15);

        return view('petugas.fines.index', compact('fines'));
    }

    private function markOverdueBorrows(): void
    {
        Borrow::where('status', Borrow::STATUS_ACTIVE)
            ->where('due_date', '<', now())
            ->update(['status' => Borrow::STATUS_LATE]);
    }
}
