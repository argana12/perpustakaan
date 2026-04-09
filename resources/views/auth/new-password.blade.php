<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Buat Password Baru') }}</h2>
        <p class="text-sm text-gray-500 mt-2">{{ __('Masukkan password baru Anda. Setelah disimpan, Anda akan diarahkan ke halaman login.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.update.otp') }}" autocomplete="off">
        @csrf

        <!-- Password Baru -->
        <div>
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="off"
                placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <x-text-input id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="off"
                placeholder="Ulangi password baru" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Simpan Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>