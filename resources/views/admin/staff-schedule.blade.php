<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📅 {{ __('Jadwal Kerja Petugas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-6">
                        Atur hari kerja para petugas di sini. Petugas yang dijadwalkan pada hari ini (berdasarkan hari aslinya seperti Senin, Selasa, dll.) otomatis akan direkomendasikan pada halaman pengisian kode di layar member.
                    </p>

                    <div class="space-y-6">
                        @forelse ($petugasList as $petugas)
                            @php
                                $diasigned = array_map('trim', explode(',', $petugas->work_days ?? ''));
                            @endphp
                            <form action="{{ route('admin.staff.schedule.update', $petugas) }}" method="POST" class="bg-gray-50 border rounded-lg p-5">
                                @csrf
                                @method('PUT')
                                
                                <div class="flex items-start justify-between flex-wrap gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 text-indigo-700 flex items-center justify-center rounded-full font-bold text-lg">
                                            {{ strtoupper(substr($petugas->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900">{{ $petugas->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $petugas->email }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_visible" value="1" class="sr-only peer" {{ $petugas->is_visible ? 'checked' : '' }}>
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-700">Akun Aktif Tampil?</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Hari Kerja (Shift):</label>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($hariList as $hari)
                                            <label class="inline-flex items-center bg-white border px-3 py-1.5 rounded-md shadow-sm cursor-pointer hover:bg-indigo-50">
                                                <input type="checkbox" name="work_days[]" value="{{ $hari }}" class="rounded text-indigo-600 focus:ring-indigo-500" 
                                                    {{ in_array($hari, $diasigned) ? 'checked' : '' }}>
                                                <span class="ms-2 text-sm text-gray-700">{{ $hari }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-5 flex justify-end">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm transition font-medium">
                                        Simpan Jadwal
                                    </button>
                                </div>
                            </form>
                        @empty
                            <div class="text-center py-10 bg-gray-50 rounded border-2 border-dashed">
                                <span class="text-4xl">👥</span>
                                <p class="mt-2 text-gray-500">Belum ada user yang terdaftar sebagai Petugas.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
