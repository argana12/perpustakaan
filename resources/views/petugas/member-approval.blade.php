<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Antrian Persetujuan Member
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Flash Messages --}}
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

            {{-- Kode yang baru di-generate --}}
            @if (session('generated'))
                @php $gen = session('generated'); @endphp
                <div class="mb-6 p-4 bg-green-50 border-2 border-green-400 rounded-lg">
                    <p class="font-bold text-green-800 text-lg mb-1">✅ Kode Aktivasi Berhasil Dibuat!</p>
                    <p class="text-sm text-gray-700">Untuk: <strong>{{ $gen['nama'] }}</strong> ({{ $gen['email'] }})</p>
                    <p class="text-3xl font-mono font-bold tracking-widest text-green-700 mt-2 bg-white border border-green-300 px-4 py-2 rounded inline-block">
                        {{ $gen['kode'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">⚠️ Catat dan berikan kode ini kepada user. Kode hanya berlaku 24 jam.</p>
                </div>
            @endif

            {{-- Tabel Antrian --}}
            @if ($pendingMembers->isEmpty())
                <div class="text-center py-16 text-gray-400">
                    <div class="text-6xl mb-4">📭</div>
                    <p class="text-lg">Tidak ada member yang menunggu persetujuan saat ini.</p>
                </div>
            @else
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OTP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Daftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($pendingMembers as $index => $member)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">✓ Terverifikasi</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $member->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('petugas.member.generate.code', $member) }}"
                                                  onsubmit="return confirm('Generate kode MURID untuk {{ $member->name }}?')">
                                                @csrf
                                                <input type="hidden" name="role" value="student">
                                                <button type="submit"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded-lg transition">
                                                    Generate Murid
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('petugas.member.generate.code', $member) }}"
                                                  onsubmit="return confirm('Generate kode GURU untuk {{ $member->name }}?')">
                                                @csrf
                                                <input type="hidden" name="role" value="teacher">
                                                <button type="submit"
                                                    class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-2 rounded-lg transition">
                                                    Generate Guru
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
