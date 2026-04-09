<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $panelTitle }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Buku & QR</h3>
                        <button type="button" id="open-add-book-modal-btn" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded">Tambah Buku</button>
                    </div>
                    <form method="POST" action="{{ route($routePrefix . '.books.labels.bulk') }}" id="bulk-label-form" class="mb-4 flex gap-2 items-center">
                        @csrf
                        <select name="size" class="border-gray-300 rounded-md text-sm">
                            <option value="80x70" selected>Label 8x7 cm</option>
                            <option value="100x50">Label 10x5 cm</option>
                        </select>
                        <button type="submit" class="px-3 py-2 bg-purple-700 text-white text-xs rounded">Cetak Label Terpilih (PDF)</button>
                    </form>
                    <table class="w-full text-sm border border-gray-200 rounded">
                        <thead class="bg-gray-100 text-gray-700 text-left">
                            <tr>
                                <th class="px-3 py-2">Pilih</th>
                                <th class="px-3 py-2">Cover</th>
                                <th class="px-3 py-2">Kode</th>
                                <th class="px-3 py-2">Judul</th>
                                <th class="px-3 py-2">Hal</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">QR</th>
                                <th class="px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($books as $book)
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" form="bulk-label-form">
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($book->cover_image)
                                            <img src="{{ route('books.cover', $book) }}" alt="cover" class="h-16 w-12 object-cover rounded">
                                        @else
                                            <span class="text-xs text-gray-400">No cover</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 font-semibold">
                                        <a href="{{ route($routePrefix . '.circulation.book.detail', $book->code) }}" class="text-indigo-600 hover:underline">{{ $book->code }}</a>
                                    </td>
                                    <td class="px-3 py-2">{{ $book->title }}</td>
                                    <td class="px-3 py-2">{{ $book->pages }}</td>
                                    <td class="px-3 py-2 uppercase">{{ $book->status }}</td>
                                    <td class="px-3 py-2">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($book->code) }}" alt="qr">
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-col gap-2">
                                            <button
                                                type="button"
                                                class="open-edit-book-modal-btn px-3 py-1 bg-indigo-600 text-white text-xs rounded text-left"
                                                data-update-url="{{ route($routePrefix . '.books.update', $book) }}"
                                                data-title="{{ $book->title }}"
                                                data-author="{{ $book->author }}"
                                                data-isbn="{{ $book->isbn }}"
                                                data-pages="{{ $book->pages }}"
                                                data-category="{{ $book->category }}"
                                                data-rack-code="{{ $book->rack_code }}"
                                                data-label-color="{{ $book->label_color }}"
                                                data-exemplar-no="{{ $book->exemplar_no ?? 1 }}"
                                                data-status="{{ $book->status }}"
                                                data-code="{{ $book->code }}"
                                                data-cover-url="{{ $book->cover_image ? route('books.cover', $book) : '' }}"
                                            >
                                                Edit Buku
                                            </button>
                                            <a href="{{ route($routePrefix . '.books.label', [$book, 'size' => '80x70']) }}" class="inline-block px-3 py-1 bg-purple-600 text-white text-xs rounded">Cetak Label PDF</a>
                                            <form method="POST" action="{{ route($routePrefix . '.books.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-500 text-white text-xs rounded w-full text-left">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-400">Belum ada data buku.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $books->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div id="add-book-modal" class="fixed inset-0 bg-black/50 hidden items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-5xl rounded-xl shadow-xl my-4">
            <div class="p-5 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Buku</h3>
                <button type="button" id="close-add-book-modal-btn" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route($routePrefix . '.books.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
                        <input type="text" name="author" value="{{ old('author') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('author')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('isbn')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Halaman</label>
                        <input type="number" min="1" name="pages" value="{{ old('pages', 1) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('pages')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}" @selected(old('category') === $category->name)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rak</label>
                        <select name="rack_code" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Rak --</option>
                            @foreach($racks as $rack)
                                <option value="{{ $rack->code }}" @selected(old('rack_code') === $rack->code)>{{ $rack->code }}{{ $rack->name ? ' - ' . $rack->name : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Eksemplar</label>
                        <input type="number" min="1" name="exemplar_no" value="{{ old('exemplar_no', 1) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="available">AVAILABLE</option>
                            <option value="borrowed">BORROWED</option>
                            <option value="reserved">RESERVED</option>
                            <option value="lost">LOST</option>
                        </select>
                        @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Cover</label>
                        <input id="cover-image-input" type="file" name="cover_image" accept="image/*" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('cover_image')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        <img id="cover-image-preview" src="#" alt="preview" class="hidden mt-2 h-24 w-20 object-cover rounded border border-gray-200">
                    </div>
                    <div class="md:col-span-6 flex justify-end gap-2">
                        <button type="button" id="cancel-add-book-modal-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm">Batal</button>
                        <x-primary-button>Tambah Buku</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-book-modal" class="fixed inset-0 bg-black/50 hidden items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-5xl rounded-xl shadow-xl my-4">
            <div class="p-5 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Buku</h3>
                <button type="button" id="close-edit-book-modal-btn" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <div class="p-5">
                <form id="edit-book-form" method="POST" action="" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    @csrf
                    @method('PUT')
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Buku</label>
                        <input id="edit-code" type="text" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku</label>
                        <input id="edit-title" type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
                        <input id="edit-author" type="text" name="author" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                        <input id="edit-isbn" type="text" name="isbn" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Halaman</label>
                        <input id="edit-pages" type="number" min="1" name="pages" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select id="edit-category" name="category" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rak</label>
                        <select id="edit-rack-code" name="rack_code" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Rak --</option>
                            @foreach($racks as $rack)
                                <option value="{{ $rack->code }}">{{ $rack->code }}{{ $rack->name ? ' - ' . $rack->name : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna Label</label>
                        <input id="edit-label-color" type="text" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Eksemplar</label>
                        <input id="edit-exemplar-no" type="number" min="1" name="exemplar_no" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="edit-status" name="status" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="available">AVAILABLE</option>
                            <option value="borrowed">BORROWED</option>
                            <option value="reserved">RESERVED</option>
                            <option value="lost">LOST</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Cover</label>
                        <input id="edit-cover-image-input" type="file" name="cover_image" accept="image/*" class="w-full border-gray-300 rounded-md shadow-sm">
                        <img id="edit-cover-image-preview" src="#" alt="preview" class="hidden mt-2 h-24 w-20 object-cover rounded border border-gray-200">
                    </div>
                    <div class="md:col-span-6 flex justify-end gap-2">
                        <button type="button" id="cancel-edit-book-modal-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const modal = document.getElementById('add-book-modal');
            const editModal = document.getElementById('edit-book-modal');
            const openModalBtn = document.getElementById('open-add-book-modal-btn');
            const closeModalBtn = document.getElementById('close-add-book-modal-btn');
            const cancelModalBtn = document.getElementById('cancel-add-book-modal-btn');
            const input = document.getElementById('cover-image-input');
            const preview = document.getElementById('cover-image-preview');
            const editButtons = document.querySelectorAll('.open-edit-book-modal-btn');
            const editForm = document.getElementById('edit-book-form');
            const closeEditModalBtn = document.getElementById('close-edit-book-modal-btn');
            const cancelEditModalBtn = document.getElementById('cancel-edit-book-modal-btn');
            const editCoverInput = document.getElementById('edit-cover-image-input');
            const editCoverPreview = document.getElementById('edit-cover-image-preview');
            const hasCreateErrors = @json($errors->has('title') || $errors->has('author') || $errors->has('isbn') || $errors->has('pages') || $errors->has('status') || $errors->has('cover_image'));

            function openModal() {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            function openEditModal() {
                if (!editModal) return;
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeEditModal() {
                if (!editModal) return;
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            openModalBtn?.addEventListener('click', openModal);
            closeModalBtn?.addEventListener('click', closeModal);
            cancelModalBtn?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
            closeEditModalBtn?.addEventListener('click', closeEditModal);
            cancelEditModalBtn?.addEventListener('click', closeEditModal);
            editModal?.addEventListener('click', (e) => {
                if (e.target === editModal) closeEditModal();
            });

            editButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    if (!editForm) return;
                    editForm.action = button.dataset.updateUrl || '';
                    document.getElementById('edit-code').value = button.dataset.code || '';
                    document.getElementById('edit-title').value = button.dataset.title || '';
                    document.getElementById('edit-author').value = button.dataset.author || '';
                    document.getElementById('edit-isbn').value = button.dataset.isbn || '';
                    document.getElementById('edit-pages').value = button.dataset.pages || 1;
                    document.getElementById('edit-category').value = button.dataset.category || '';
                    document.getElementById('edit-rack-code').value = button.dataset.rackCode || '';
                    document.getElementById('edit-label-color').value = button.dataset.labelColor || '-';
                    document.getElementById('edit-exemplar-no').value = button.dataset.exemplarNo || 1;
                    document.getElementById('edit-status').value = button.dataset.status || 'available';

                    if (editCoverPreview) {
                        if (button.dataset.coverUrl) {
                            editCoverPreview.src = button.dataset.coverUrl;
                            editCoverPreview.classList.remove('hidden');
                        } else {
                            editCoverPreview.src = '#';
                            editCoverPreview.classList.add('hidden');
                        }
                    }
                    if (editCoverInput) {
                        editCoverInput.value = '';
                    }
                    openEditModal();
                });
            });

            if (hasCreateErrors) {
                openModal();
            }

            if (!input || !preview) return;

            input.addEventListener('change', (event) => {
                const file = event.target.files && event.target.files[0];
                if (!file) {
                    preview.classList.add('hidden');
                    preview.src = '#';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target?.result || '#';
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });

            editCoverInput?.addEventListener('change', (event) => {
                const file = event.target.files && event.target.files[0];
                if (!file || !editCoverPreview) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    editCoverPreview.src = e.target?.result || '#';
                    editCoverPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
</x-app-layout>
