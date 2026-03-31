<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Lupa Kata Sandi?') }}</h2>
    </div>

    <div class="mb-6 text-sm text-gray-600 leading-relaxed text-center">
        {{ __('Jangan khawatir. Cukup beri tahu kami alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda agar bisa kembali mengakses perpustakaan.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan email terdaftar Anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Kirim Tautan Reset Sandi') }}
            </x-primary-button>
        </div>
        
        <div class="mt-4 text-center text-sm">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">{{ __('Kembali ke halaman Masuk') }}</a>
        </div>
    </form>
</x-guest-layout>