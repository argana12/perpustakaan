<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Label & Rak</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 space-y-6">
            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
                    <h3 class="font-semibold mb-3">Master Kategori Buku</h3>
                    <form method="POST" action="{{ route('admin.book.categories.store') }}" class="flex gap-2 mb-4">
                        @csrf
                        <input name="name" placeholder="NOVEL" class="w-full border-gray-300 rounded-md" required>
                        <select name="label_color" class="w-full border-gray-300 rounded-md">
                            <option value="">-- Warna Label Default --</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->name }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">Tambah</button>
                    </form>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        @foreach($categories as $category)
                            <div class="flex justify-between items-center border rounded p-2 text-sm">
                                <div>{{ $category->name }} {{ $category->label_color ? '- ' . $category->label_color : '' }}</div>
                                <form method="POST" action="{{ route('admin.book.categories.destroy', $category) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-500 text-white rounded text-xs">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-semibold mb-3">Master Warna Label</h3>
                    <form method="POST" action="{{ route('admin.label.colors.store') }}" class="flex gap-2 mb-4">
                        @csrf
                        <input name="name" placeholder="Biru" class="w-full border-gray-300 rounded-md" required>
                        <input name="hex" placeholder="#0000FF" class="w-full border-gray-300 rounded-md">
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">Tambah</button>
                    </form>
                    <div class="space-y-2">
                        @foreach($colors as $color)
                            <div class="flex justify-between items-center border rounded p-2 text-sm">
                                <div>{{ $color->name }} {{ $color->hex ? '(' . $color->hex . ')' : '' }}</div>
                                <form method="POST" action="{{ route('admin.label.colors.destroy', $color) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-500 text-white rounded text-xs">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-semibold mb-3">Master Rak</h3>
                    <form method="POST" action="{{ route('admin.racks.store') }}" class="flex gap-2 mb-4">
                        @csrf
                        <input name="code" placeholder="R1-A3" class="w-full border-gray-300 rounded-md" required>
                        <input name="name" placeholder="Rak Novel Barat" class="w-full border-gray-300 rounded-md">
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">Tambah</button>
                    </form>
                    <div class="space-y-2">
                        @foreach($racks as $rack)
                            <div class="flex justify-between items-center border rounded p-2 text-sm">
                                <div>{{ $rack->code }} {{ $rack->name ? '- ' . $rack->name : '' }}</div>
                                <form method="POST" action="{{ route('admin.racks.destroy', $rack) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-500 text-white rounded text-xs">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
