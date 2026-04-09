<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserOtpController;
use App\Http\Controllers\Admin\StaffScheduleController;
use App\Http\Controllers\Admin\LabelMasterController;
use App\Http\Controllers\Petugas\MemberApprovalController;
use App\Http\Controllers\Petugas\CirculationController;
use App\Http\Controllers\Member\SummaryController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Models\Book;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Manajemen OTP User
    Route::get('/otp-management', [UserOtpController::class, 'index'])->name('otp.index');
    Route::post('/otp-management/{user}/unlock', [UserOtpController::class, 'unlock'])->name('otp.unlock');
    Route::post('/otp-management/{user}/reset', [UserOtpController::class, 'resetFull'])->name('otp.reset');

    // Jadwal Petugas
    Route::get('/staff-schedule', [StaffScheduleController::class, 'index'])->name('staff.schedule.index');
    Route::put('/staff-schedule/{user}', [StaffScheduleController::class, 'update'])->name('staff.schedule.update');

    // Persetujuan All Users (CRM)
    Route::get('/pending-users', [UserOtpController::class, 'pendingUsers'])->name('pending.users');
    Route::post('/pending-users/{user}/generate-code', [UserOtpController::class, 'generateCode'])->name('users.generate.code');
    Route::post('/pending-users/{user}/reject', [UserOtpController::class, 'rejectUser'])->name('users.reject');
    Route::delete('/users/{user}', [UserOtpController::class, 'destroyUser'])->name('users.destroy');

    // Manajemen Kelas (hanya admin)
    Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
    Route::delete('/classes/{schoolClass}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');

    // Manajemen Jurusan (hanya admin)
    Route::post('/majors', [MajorController::class, 'store'])->name('majors.store');
    Route::delete('/majors/{major}', [MajorController::class, 'destroy'])->name('majors.destroy');

    // Semua User (Admin)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Master Data Kelas & Jurusan
    Route::get('/master-data', [MasterDataController::class, 'index'])->name('master.data.index');
    Route::get('/label-master', [LabelMasterController::class, 'index'])->name('label.master.index');
    Route::post('/label-colors', [LabelMasterController::class, 'storeColor'])->name('label.colors.store');
    Route::delete('/label-colors/{labelColor}', [LabelMasterController::class, 'destroyColor'])->name('label.colors.destroy');
    Route::post('/racks', [LabelMasterController::class, 'storeRack'])->name('racks.store');
    Route::delete('/racks/{rack}', [LabelMasterController::class, 'destroyRack'])->name('racks.destroy');
    Route::post('/book-categories', [LabelMasterController::class, 'storeCategory'])->name('book.categories.store');
    Route::delete('/book-categories/{bookCategory}', [LabelMasterController::class, 'destroyCategory'])->name('book.categories.destroy');

    // Manajemen Buku
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/books/{book}/label', [BookController::class, 'printLabel'])->name('books.label');
    Route::post('/books/labels/bulk', [BookController::class, 'printBulkLabel'])->name('books.labels.bulk');

    // Detail Buku (akses admin)
    Route::get('/circulation/book/{code}', [CirculationController::class, 'showBookDetail'])->name('circulation.book.detail');
});

/*
|--------------------------------------------------------------------------
| PETUGAS Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {

    Route::get('/', function () {
        return view('petugas.dashboard');
    })->name('dashboard');

    // Approval Member
    Route::get('/member-approval', [MemberApprovalController::class, 'index'])->name('member.approval');
    Route::post('/member-approval/{user}/generate-code', [MemberApprovalController::class, 'generateCode'])->name('member.generate.code');
    Route::post('/member-approval/{user}/unlock', [MemberApprovalController::class, 'unlock'])->name('member.unlock');

    // Lihat semua user
    Route::get('/users', [MemberApprovalController::class, 'allUsers'])->name('users');
    Route::delete('/members/{user}', [MemberApprovalController::class, 'destroyUser'])->name('member.destroy');

    // Manajemen Buku
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/books/{book}/label', [BookController::class, 'printLabel'])->name('books.label');
    Route::post('/books/labels/bulk', [BookController::class, 'printBulkLabel'])->name('books.labels.bulk');

    // Sirkulasi Peminjaman dan Pengembalian
    Route::get('/circulation/loan', [CirculationController::class, 'loanMode'])->name('circulation.loan');
    Route::post('/circulation/loan', [CirculationController::class, 'processLoan'])->name('circulation.loan.store');
    Route::get('/circulation/scan/{code}', [CirculationController::class, 'scannedBookData'])->name('circulation.scan.book');
    Route::get('/circulation/borrower/search', [CirculationController::class, 'searchBorrower'])->name('circulation.borrower.search');
    Route::get('/circulation/borrower/{user}', [CirculationController::class, 'borrowerSummary'])->name('circulation.borrower.summary');
    Route::get('/circulation/return', [CirculationController::class, 'returnMode'])->name('circulation.return');
    Route::get('/circulation/return/scan/{code}', [CirculationController::class, 'scannedReturnData'])->name('circulation.return.scan.book');
    Route::post('/circulation/return', [CirculationController::class, 'processReturn'])->name('circulation.return.store');
    Route::post('/queues/{bookQueue}/call', [CirculationController::class, 'callQueue'])->name('queues.call');
    Route::post('/queues/{bookQueue}/notify', [CirculationController::class, 'notifyQueue'])->name('queues.notify');
    Route::post('/queues/{bookQueue}/complete', [CirculationController::class, 'completeQueue'])->name('queues.complete');
    Route::get('/circulation/book/{code}', [CirculationController::class, 'showBookDetail'])->name('circulation.book.detail');
    Route::post('/borrows/{borrow}/pay-fine', [CirculationController::class, 'payFine'])->name('borrows.pay-fine');
    Route::post('/summaries/{summary}/approve', [CirculationController::class, 'approveSummary'])->name('summaries.approve');
    Route::post('/summaries/{summary}/reject', [CirculationController::class, 'rejectSummary'])->name('summaries.reject');
    Route::get('/fines', [CirculationController::class, 'fines'])->name('fines.index');
});

/*
|--------------------------------------------------------------------------
| MEMBER / General Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $books = Book::orderBy('title')->paginate(10);

    return view('dashboard', compact('books'));
})->middleware(['auth', 'active'])->name('dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/books/{book}/cover', [BookController::class, 'cover'])->name('books.cover');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'active', 'role:member'])->group(function () {
    Route::get('/member/books', [BookController::class, 'memberIndex'])->name('member.books.index');
    Route::post('/member/books/{book}/queue', [BookController::class, 'queue'])->name('member.books.queue');
    Route::post('/member/borrows/{borrow}/summary', [SummaryController::class, 'store'])->name('member.summary.store');
});

require __DIR__.'/auth.php';
