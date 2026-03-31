<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Pendaftaran Anggota') }}</h2>
        <p class="text-sm text-gray-500 mt-2">{{ __('Bergabunglah untuk menikmati ribuan koleksi literatur digital secara gratis.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama sesuai identitas" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Daftar Menjadi Anggota') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center text-sm text-gray-600">
            {{ __('Sudah terdaftar?') }} 
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">{{ __('Masuk di sini') }}</a>
        </div>
    </form>
</x-guest-layout>