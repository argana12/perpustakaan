<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Pendaftaran') }}</h2>
        <p class="text-sm text-gray-500 mt-2">{{ __('Bergabunglah dengan perpustakaan sekolah.') }}</p>
    </div>

    @if(isset($google_email))
        <form method="POST" action="{{ url('auth/google/register') }}" id="register-google-form" autocomplete="off">
            @csrf
            <input type="hidden" name="email" value="{{ $google_email }}">
            <input type="hidden" name="google_id" value="{{ $google_id }}">

            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="off" placeholder="Ketik nama lengkap sesuai identitas" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-700">
                Anda mendaftar menggunakan akun Google: <strong>{{ $google_email }}</strong>
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center py-3">
                    {{ __('Lanjutkan Pendaftaran') }}
                </x-primary-button>
            </div>
        </form>
    @else
        <form method="POST" action="{{ route('register') }}" id="register-form" autocomplete="off">
        @csrf

        {{-- Nama --}}
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="off" placeholder="Nama sesuai identitas" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="off" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>


        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="off" placeholder="Minimal 6 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="off" placeholder="Ulangi kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-6">
            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <img class="h-5 w-5 mr-2" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                {{ __('Daftar dengan Google') }}
            </a>
        </div>

        <div class="mt-4 text-center text-sm text-gray-600">
            {{ __('Sudah terdaftar?') }}
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">{{ __('Masuk di sini') }}</a>
        </div>
    </form>
    @endif

</x-guest-layout>