<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏢 {{ __('Master Data Kelas & Jurusan') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @foreach (['class_added', 'class_deleted', 'major_added', 'major_deleted'] as $key)
                @if (session($key))
                    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded">
                        {{ session($key) }}
                    </div>
                @endif
            @endforeach

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Panel Kelas --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        🏫 Manajemen Kelas
                        <span class="text-xs font-normal bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full">Admin Only</span>
                    </h3>

                    {{-- Form Tambah Kelas --}}
                    <form method="POST" action="{{ route('admin.classes.store') }}" class="flex gap-2 mb-6">
                        @csrf
                        <input type="text" name="name" placeholder="cth: X, XI IPA 1, XII IPS 2"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none uppercase"
                            style="text-transform:uppercase" required>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition whitespace-nowrap">
                            + Tambah
                        </button>
                    </form>

                    {{-- List Kelas --}}
                    <div class="max-h-96 overflow-y-auto space-y-2">
                        @forelse ($classes as $class)
                            <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                                <span class="text-sm font-medium text-gray-700">{{ $class->name }}</span>
                                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                    onsubmit="return confirm('Hapus kelas {{ $class->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs transition font-semibold">
                                        ✕ Hapus
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-6 border border-dashed border-gray-200 rounded-lg">Belum ada kelas. Tambahkan di atas.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Panel Jurusan --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        📚 Manajemen Jurusan
                        <span class="text-xs font-normal bg-purple-100 text-purple-600 px-2 py-0.5 rounded-full">Admin Only</span>
                    </h3>

                    {{-- Form Tambah Jurusan --}}
                    <form method="POST" action="{{ route('admin.majors.store') }}" class="flex gap-2 mb-6">
                        @csrf
                        <input type="text" name="name" placeholder="cth: IPA, IPS, Teknik Informatika"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300 focus:outline-none"
                            required>
                        <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-4 py-2 rounded-lg transition whitespace-nowrap">
                            + Tambah
                        </button>
                    </form>

                    {{-- List Jurusan --}}
                    <div class="max-h-96 overflow-y-auto space-y-2">
                        @forelse ($majors as $major)
                            <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                                <span class="text-sm font-medium text-gray-700">{{ $major->name }}</span>
                                <form method="POST" action="{{ route('admin.majors.destroy', $major) }}"
                                    onsubmit="return confirm('Hapus jurusan {{ $major->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs transition font-semibold">
                                        ✕ Hapus
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-6 border border-dashed border-gray-200 rounded-lg">Belum ada jurusan. Tambahkan di atas.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
