<x-guest-layout>
    <div class="text-center mb-6">
        <div class="text-5xl mb-4">⏳</div>
        <h2 class="text-2xl font-bold text-gray-800">Menunggu Persetujuan</h2>
    </div>

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

    @php
        $userId = session('verify_user_id');
        $user   = $userId ? \App\Models\User::find($userId) : null;
    @endphp

    @if ($user)
        <div class="bg-gray-50 border rounded-lg p-4 mb-6 text-sm text-gray-700">
            <p><span class="font-semibold">Nama:</span> {{ $user->name }}</p>
            <p class="mt-1"><span class="font-semibold">Email:</span> {{ $user->email }}</p>
            <p class="mt-1">
                <span class="font-semibold">Status:</span>
                @if ($user->status === 'pending')
                    <span class="inline-block bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs">Menunggu petugas/admin</span>
                @elseif ($user->status === 'approved')
                    <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">Sudah disetujui — masukkan kode</span>
                @endif
            </p>
        </div>

        @if ($user->status === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800 mb-4">
                <p class="font-semibold mb-1">📋 Langkah selanjutnya:</p>
                <p>Silakan <strong>hubungi petugas perpustakaan</strong> yang sedang berjaga dan berikan informasi email kamu. Petugas akan memverifikasi dan mengaktifkan kodemu.</p>
                
                @if((isset($petugasHariIni) && $petugasHariIni->count() > 0) || (isset($petugasLainnya) && $petugasLainnya->count() > 0))
                    <div class="mt-4 pt-4 border-t border-yellow-200">
                        <p class="font-semibold mb-3 text-yellow-900">👥 Spesial Hari Ini ({{ $hariIni }}):</p>
                        
                        @if($petugasHariIni->count() > 0)
                            <div class="mb-4">
                                <ul class="space-y-2">
                                    @foreach($petugasHariIni as $petugas)
                                        <li class="bg-indigo-50 p-2 rounded border border-indigo-200 flex items-center justify-between shadow-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg">⭐</span>
                                                <span class="font-bold text-indigo-900">{{ $petugas->name }}</span>
                                            </div>
                                            <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full font-semibold">Sedang Berjaga</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-yellow-800 italic mb-4 bg-yellow-100/50 p-2 rounded">Belum ada petugas yang dijadwalkan khusus untuk hari ini. Silakan hubungi petugas lainnya.</p>
                        @endif

                        @if($petugasLainnya->count() > 0)
                            <p class="font-semibold mb-2 mt-2 text-xs text-yellow-800 uppercase tracking-wider">Jadwal Petugas Lainnya:</p>
                            <ul class="space-y-2 opacity-75 grayscale-[30%]">
                                @foreach($petugasLainnya as $petugas)
                                    <li class="bg-white p-2 rounded border border-yellow-100 flex items-center justify-between">
                                        <span class="font-medium text-gray-800">{{ $petugas->name }}</span>
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-600">{{ $petugas->work_days ?: 'Belum diatur' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @else
                    <p class="mt-2 text-xs text-gray-500 italic">Belum ada jadwal petugas yang tersedia untuk ditampilkan.</p>
                @endif
            </div>
        @elseif ($user->status === 'approved')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800 mb-4">
                <p class="font-semibold mb-1">✅ Kode aktivasi sudah disiapkan!</p>
                <p>Masukkan kode yang diberikan petugas untuk mengaktifkan akun kamu.</p>
            </div>
            <form method="POST" action="{{ route('registration.code.verify') }}" class="bg-white border rounded-lg p-4 mb-4">
                @csrf
                <div>
                    <x-input-label for="code" :value="__('Kode Aktivasi')" />
                    <x-text-input
                        id="code"
                        class="block mt-1 w-full text-center text-2xl tracking-widest font-mono uppercase border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        type="text"
                        name="code"
                        :value="old('code')"
                        maxlength="7"
                        required
                        autofocus
                        placeholder="XXXX999"
                    />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <p class="text-xs text-gray-400 mt-2 text-center">
                    Percobaan tersisa: {{ 5 - $user->code_attempt }} dari 5
                </p>
                <div class="mt-4">
                    <x-primary-button class="w-full justify-center py-2">
                        {{ __('Verifikasi Kode') }}
                    </x-primary-button>
                </div>
            </form>
        @endif

        @if ($user->pending_expired_at && in_array($user->status, ['pending', 'approved']))
            <p class="text-xs text-gray-400 text-center mt-3">
                Masa berlaku permohonan: {{ $user->pending_expired_at->format('d M Y H:i') }}
            </p>
        @endif
    @else
        <div class="bg-gray-50 border rounded-lg p-4 text-sm text-gray-600">
            <p>Sesi habis atau tidak valid. Proses pendaftaran mungkin telah selesai atau diulang.</p>
        </div>
    @endif

    <div class="mt-6 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">← Kembali ke Login</a>
    </div>
</x-guest-layout>
