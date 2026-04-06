<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Kode Petugas (Staff Codes)
            </h2>
            <form method="POST" action="{{ route('admin.staff.codes.generate') }}">
                @csrf
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    + Generate Kode Baru
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4">

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Kode yang baru dibuat --}}
            @if (session('generated_staff_code'))
                <div class="mb-6 p-4 bg-indigo-50 border-2 border-indigo-400 rounded-lg text-center">
                    <p class="font-bold text-indigo-800 mb-1">🎉 Kode Petugas Baru Berhasil Dibuat!</p>
                    <p class="text-4xl font-mono font-bold tracking-widest text-indigo-700 mt-2">
                        {{ session('generated_staff_code') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">Berikan kode ini ke calon petugas. Kode hanya bisa dipakai 1 kali.</p>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dipakai Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($codes as $index => $code)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-lg tracking-widest text-gray-800">{{ $code->code }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($code->is_used)
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-500 rounded-full">Sudah dipakai</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $code->usedByUser?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $code->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if (!$code->is_used)
                                        <form method="POST" action="{{ route('admin.staff.codes.destroy', $code) }}"
                                              onsubmit="return confirm('Hapus kode {{ $code->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-gray-300 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                    Belum ada kode petugas. Klik "Generate Kode Baru" untuk membuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
