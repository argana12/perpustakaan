<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Masuk ke Perpustakaan') }}</h2>
        <p class="text-sm text-gray-500 mt-2">{{ __('Silakan masuk untuk mengakses dan meminjam koleksi buku digital kami.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

     <div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />

    <div class="relative">
        <input id="password" type="password" name="password"
            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm pr-10"
            required />

        <button type="button"
            onclick="togglePassword()"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">

            <!-- Icon mata -->
            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0
                         3 3 0 016 0z" />

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M2.458 12C3.732 7.943
                         7.523 5 12 5
                         c4.477 0 8.268 2.943
                         9.542 7
                         -1.274 4.057
                         -5.065 7
                         -9.542 7
                         -4.477 0
                         -8.268-2.943
                         -9.542-7z" />
            </svg>

        </button>
    </div>
</div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-indigo-600 hover:text-indigo-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-6">
            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <img class="h-5 w-5 mr-2" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                {{ __('Lanjutkan dengan Google') }}
            </a>
        </div>
        
        <div class="mt-4 text-center text-sm text-gray-600">
            {{ __('Belum menjadi anggota?') }} 
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">{{ __('Daftar sekarang') }}</a>
        </div>
    </form>
</x-guest-layout>

<script>
function togglePassword() {

    const password = document.getElementById("password");

    if (password.type === "password") {
        password.type = "text";
    } else {
        password.type = "password";
    }

}
</script>