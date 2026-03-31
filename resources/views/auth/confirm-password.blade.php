<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Area Keamanan Ekstra') }}</h2>
    </div>

    <div class="mb-6 text-sm text-gray-600 text-center bg-yellow-50 p-3 rounded border border-yellow-200">
        {{ __('Ini adalah area yang aman di aplikasi perpustakaan. Harap konfirmasi kata sandi Anda sebelum melanjutkan ke halaman berikutnya.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Masukkan kata sandi Anda" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Konfirmasi Kata Sandi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>