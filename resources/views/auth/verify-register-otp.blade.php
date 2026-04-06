<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Verifikasi OTP') }}</h2>
        <p class="text-sm text-gray-500 mt-2">{{ __('Kode OTP telah dikirim ke email kamu. Berlaku selama 5 menit.') }}</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    {{-- Error --}}
    @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Countdown Timer --}}
    @if ($otp_expired_at)
        <div class="mb-4 text-center">
            <p class="text-sm text-gray-600">OTP kadaluarsa dalam:
                <strong class="text-red-600" id="timer"></strong>
            </p>
        </div>
    @else
        <div class="mb-4 text-center">
            <p class="text-sm text-orange-500">OTP kadaluarsa atau belum dikirim.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('register.otp.verify') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('Kode OTP')" />
            <x-text-input
                id="otp"
                class="block mt-1 w-full text-center tracking-widest text-lg"
                type="text"
                name="otp"
                maxlength="6"
                required
                autofocus
                placeholder="Masukkan 6 digit kode OTP"
            />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Verifikasi & Aktifkan Akun') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Tombol Kirim Ulang OTP --}}
    @php
        $canResend = !$otp_next_allowed_at || now()->gte($otp_next_allowed_at);
    @endphp

    <form method="POST" action="{{ route('register.otp.resend') }}" class="mt-4">
        @csrf
        @if ($canResend)
            <button type="submit"
                class="w-full text-center text-sm text-indigo-600 hover:underline bg-transparent border-none cursor-pointer">
                Kirim Ulang OTP
            </button>
        @else
            <p class="text-center text-sm text-gray-500" id="resend-timer-text">
                Kirim ulang tersedia dalam: <strong class="text-gray-700">...</strong>
            </p>
            <button type="submit" disabled
                class="w-full text-center text-sm text-gray-400 cursor-not-allowed">
                Kirim Ulang OTP
            </button>
        @endif
    </form>

    <div class="mt-4 text-center text-sm text-gray-600">
        {{ __('Sudah punya akun?') }}
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">
            {{ __('Masuk di sini') }}
        </a>
    </div>
</x-guest-layout>

@if ($otp_expired_at)
<script>
    let secondsLeft = {{ max(0, \Carbon\Carbon::parse($otp_expired_at)->getTimestamp() - now()->getTimestamp()) }};
    
    let x = setInterval(function () {
        secondsLeft--;

        if (secondsLeft <= 0) {
            clearInterval(x);
            document.getElementById("timer").innerHTML = "EXPIRED";
            return;
        }

        let minutes = Math.floor(secondsLeft / 60);
        let seconds = secondsLeft % 60;

        document.getElementById("timer").innerHTML =
            String(minutes).padStart(2, '0') + "m " +
            String(seconds).padStart(2, '0') + "s";
    }, 1000);
</script>
@endif

<script>
    let resendSecondsLeft = {{ $otp_next_allowed_at ? max(0, \Carbon\Carbon::parse($otp_next_allowed_at)->getTimestamp() - now()->getTimestamp()) : 0 }};
    
    if (resendSecondsLeft > 0) {
        let y = setInterval(function () {
            resendSecondsLeft--;
            if (resendSecondsLeft <= 0) {
                clearInterval(y);
                window.location.reload(); 
            } else {
                let m = Math.floor(resendSecondsLeft / 60);
                let s = resendSecondsLeft % 60;
                let text = (m > 0 ? String(m).padStart(2, '0') + "m " : "") + String(s).padStart(2, '0') + "s";
                let el = document.getElementById("resend-timer-text");
                if (el) el.innerHTML = "Kirim ulang tersedia dalam: <strong class='text-gray-700'>" + text + "</strong>";
            }
        }, 1000);
    }
</script>
