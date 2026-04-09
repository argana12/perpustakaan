<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Perpustakaan Digital</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl shadow text-white">
                <div class="p-6 md:p-7 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-semibold">Selamat datang di perpustakaan digital</h3>
                        <p class="text-indigo-100 mt-1 text-sm">Cari buku lebih cepat, cek status pinjaman, dan upload rangkuman langsung dari satu halaman.</p>
                    </div>
                    <a href="#pinjaman-saya" class="inline-flex items-center justify-center px-4 py-2 bg-white text-indigo-700 rounded-md text-sm font-semibold">Lihat Pinjaman Saya</a>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500">Buku di halaman ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $books->count() }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500">Tersedia</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $books->where('status', 'available')->count() }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500">Tidak tersedia</p>
                    <p class="text-2xl font-semibold text-amber-600">{{ $books->where('status', '!=', 'available')->count() }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500">Pinjaman aktif saya</p>
                    <p class="text-2xl font-semibold text-indigo-600">{{ $activeBorrows->count() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="mb-4 flex flex-col md:flex-row gap-2 md:items-center md:justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Katalog Buku</h3>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input id="book-search-input" type="text" placeholder="Cari judul, kode, penulis, ISBN..." class="w-full sm:w-72 border-gray-300 rounded-md text-sm">
                            <select id="book-status-filter" class="w-full sm:w-44 border-gray-300 rounded-md text-sm">
                                <option value="all">Semua status</option>
                                <option value="available">AVAILABLE</option>
                                <option value="borrowed">BORROWED</option>
                                <option value="reserved">RESERVED</option>
                                <option value="lost">LOST</option>
                            </select>
                        </div>
                    </div>

                    <div id="books-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @forelse ($books as $book)
                            @php
                                $status = strtolower($book->status);
                                $statusClass = match ($status) {
                                    'available' => 'bg-green-100 text-green-700',
                                    'borrowed' => 'bg-amber-100 text-amber-700',
                                    'reserved' => 'bg-blue-100 text-blue-700',
                                    'lost' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                                $myQueue = $myQueues[$book->id] ?? null;
                                $queueStatus = $myQueue?->status;
                                $queueClass = match ($queueStatus) {
                                    'waiting' => 'bg-amber-100 text-amber-700',
                                    'ready' => 'bg-blue-100 text-blue-700',
                                    'called' => 'bg-indigo-100 text-indigo-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <article class="book-item border border-gray-200 rounded-lg p-4 flex gap-4 bg-white" data-search="{{ strtolower(($book->title ?? '') . ' ' . ($book->code ?? '') . ' ' . ($book->author ?? '') . ' ' . ($book->isbn ?? '')) }}" data-status="{{ $status }}">
                                <div class="shrink-0">
                                    @if($book->cover_image)
                                        <img src="{{ route('books.cover', $book) }}" class="h-24 w-16 object-cover rounded border border-gray-200" alt="cover">
                                    @else
                                        <div class="h-24 w-16 rounded border border-dashed border-gray-300 text-[10px] text-gray-400 flex items-center justify-center text-center px-1">No Cover</div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $book->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $book->code }} • {{ $book->author ?: 'Penulis tidak tersedia' }}</p>
                                    <p class="text-xs text-gray-500">Hal: {{ $book->pages }} • ISBN: {{ $book->isbn ?: '-' }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2 items-center">
                                        <span class="px-2 py-1 rounded text-[11px] font-semibold uppercase {{ $statusClass }}">{{ $book->status }}</span>
                                        @if($myQueue)
                                            <span class="px-2 py-1 rounded text-[11px] font-semibold uppercase {{ $queueClass }}">Antrian {{ $myQueue->status }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        @if($myQueue)
                                            <button type="button" class="px-3 py-1.5 bg-gray-200 text-gray-600 text-xs rounded cursor-not-allowed" disabled>
                                                Sudah di antrian
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('member.books.queue', $book) }}">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded">
                                                    {{ $book->status === 'available' ? 'Booking Sekarang' : 'Masuk Antrian' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full px-4 py-10 text-center text-gray-400 border border-dashed rounded-lg">Belum ada data buku.</div>
                        @endforelse
                    </div>

                    <p id="books-empty-state" class="hidden text-sm text-gray-500 mt-4">Tidak ada buku yang cocok dengan filter.</p>

                    <div class="mt-6">
                        {{ $books->links() }}
                    </div>
                </div>
            </div>

            <div id="pinjaman-saya" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Buku Sedang Dipinjam Saya</h3>
                    <div class="space-y-3">
                        @forelse ($activeBorrows as $borrow)
                            @php
                                $borrowStatusClass = strtolower($borrow->status) === 'late' ? 'text-red-600' : 'text-green-600';
                                $summaryStatus = $borrow->summary?->status;
                                $summaryClass = match ($summaryStatus) {
                                    'approved' => 'text-green-700',
                                    'rejected' => 'text-red-700',
                                    'pending' => 'text-amber-700',
                                    default => 'text-amber-700',
                                };
                            @endphp
                            <div class="border rounded-lg p-4">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $borrow->book->title }} <span class="text-gray-500">({{ $borrow->book->code }})</span></p>
                                        <p class="text-sm text-gray-600">
                                            Deadline: {{ $borrow->due_date->format('d M Y H:i') }} |
                                            Status: <span class="uppercase font-semibold {{ $borrowStatusClass }}">{{ $borrow->status }}</span>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Rangkuman:
                                            @if($borrow->summary)
                                                <span class="font-semibold uppercase {{ $summaryClass }}">{{ $borrow->summary->status }}</span>
                                                @if($borrow->summary->review_note)
                                                    <span class="text-xs text-gray-500">({{ $borrow->summary->review_note }})</span>
                                                @endif
                                            @else
                                                <span class="text-amber-600 font-semibold">BELUM UPLOAD</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('member.summary.store', $borrow) }}" enctype="multipart/form-data" class="mt-3 flex flex-col sm:flex-row gap-2 sm:items-center">
                                    @csrf
                                    <input type="file" name="file" accept="image/*" class="border-gray-300 rounded-md text-sm" required>
                                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white text-xs rounded">Upload / Revisi Rangkuman</button>
                                </form>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500 border border-dashed rounded-lg p-6 text-center">Tidak ada pinjaman aktif saat ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const searchInput = document.getElementById('book-search-input');
            const statusFilter = document.getElementById('book-status-filter');
            const items = Array.from(document.querySelectorAll('.book-item'));
            const emptyState = document.getElementById('books-empty-state');

            if (!searchInput || !statusFilter || !items.length || !emptyState) return;

            function applyFilter() {
                const q = (searchInput.value || '').trim().toLowerCase();
                const status = statusFilter.value;
                let visible = 0;

                items.forEach((item) => {
                    const text = item.dataset.search || '';
                    const itemStatus = item.dataset.status || '';
                    const matchText = q === '' || text.includes(q);
                    const matchStatus = status === 'all' || itemStatus === status;
                    const show = matchText && matchStatus;
                    item.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                emptyState.classList.toggle('hidden', visible > 0);
            }

            searchInput.addEventListener('input', applyFilter);
            statusFilter.addEventListener('change', applyFilter);
        })();
    </script>
</x-app-layout>
