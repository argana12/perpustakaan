<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔐 {{ __('Manajemen OTP User') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Status --}}
            @if (session('status'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-6">
                        Halaman ini digunakan untuk mereset cooldown OTP user yang terblokir karena terlalu sering meminta OTP.
                    </p>

                    <table class="w-full text-sm border border-gray-200 rounded overflow-hidden">
                        <thead class="bg-gray-100 text-gray-700 text-left">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Attempt</th>
                                <th class="px-4 py-3">Cooldown s/d</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                @php
                                    $isBlocked = $user->otp_next_allowed_at && now()->lt($user->otp_next_allowed_at);
                                @endphp
                                <tr class="{{ $isBlocked ? 'bg-red-50' : '' }}">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-bold
                                            {{ $user->otp_attempt >= 6 ? 'bg-red-100 text-red-700' :
                                               ($user->otp_attempt >= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $user->otp_attempt ?? 0 }}x
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if ($user->otp_next_allowed_at)
                                            {{ $user->otp_next_allowed_at->format('d M Y H:i:s') }}
                                        @else
                                            <span class="text-gray-400">–</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->status === 'suspended')
                                            <span class="px-2 py-1 bg-red-800 text-white rounded text-xs font-bold">❌ Suspend</span>
                                        @elseif ($user->otp_unlocked)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">✅ Unlocked</span>
                                        @elseif ($isBlocked)
                                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">🔒 Diblokir</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">✓ Normal</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2 justify-center">
                                            {{-- Unlock Status --}}
                                            <form method="POST" action="{{ route('admin.otp.unlock', $user) }}"
                                                onsubmit="return confirm('Buka blokir / Reset cooldown untuk {{ $user->name }}?')">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs rounded bg-yellow-500 hover:bg-yellow-600 text-white font-semibold transition">
                                                    🔓 Buka Blokir
                                                </button>
                                            </form>

                                            {{-- Reset Penuh --}}
                                            <form method="POST" action="{{ route('admin.otp.reset', $user) }}"
                                                onsubmit="return confirm('RESET PENUH semua data OTP {{ $user->name }}? Ini akan menghapus semua history OTP.')">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs rounded bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                                                    🗑 Reset Penuh
                                                </button>
                                            </form>

                                            {{-- Hapus User --}}
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('HAPUS PERMANEN akun {{ $user->name }}? Semua data terkait juga akan terhapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs rounded bg-gray-800 hover:bg-gray-900 text-white font-semibold transition">
                                                    ❌ Hapus User
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-400">Tidak ada user ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
