<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Verifikasi Email Anda') }}</h2>
    </div>

    <div class="mb-4 text-sm text-gray-600 leading-relaxed text-center">
        {{ __('Terima kasih telah mendaftar menjadi anggota perpustakaan digital! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda.') }}
        <br><br>
        {{ __('Jika Anda tidak menerima email tersebut, klik tombol di bawah untuk meminta tautan baru.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded text-center border border-green-200">
            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
            @csrf
            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Kirim Ulang Email Verifikasi') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full text-center underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Keluar (Log Out)') }}
            </button>
        </form>
    </div>
</x-guest-layout>