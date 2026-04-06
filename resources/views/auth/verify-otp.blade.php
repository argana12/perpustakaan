<form method="POST" action="{{ route('password.otp.verify') }}">
    @csrf

    {{-- Status (OTP dikirim ulang dll) --}}
    @if (session('status'))
        <p style="color: green;">{{ session('status') }}</p>
    @endif

    {{-- Error messages --}}
    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <input type="text" name="otp" placeholder="Masukkan OTP" required>

    {{-- Countdown timer kalau ada OTP aktif --}}
    @if ($expired_at)
        <p>OTP kadaluarsa dalam: <strong><span id="timer"></span></strong></p>
    @else
        <p style="color: orange;">OTP kadaluarsa atau belum dikirim.</p>
    @endif

    <button type="submit">Verifikasi OTP</button>
</form>

{{-- Tombol kirim ulang OTP --}}
@php
    $canResend = !$next_allowed_at || now()->gte($next_allowed_at);
@endphp

<form method="POST" action="{{ route('password.otp.resend') }}" style="margin-top: 10px;">
    @csrf
    @if ($canResend)
        <button type="submit">Kirim Ulang OTP</button>
    @else
        <p id="resend-timer-text">Kirim ulang tersedia dalam: <strong>...</strong></p>
        <button type="submit" disabled>Kirim Ulang OTP</button>
    @endif
</form>

{{-- Countdown timer script --}}
@if ($expired_at)
<script>
    let secondsLeft = {{ max(0, \Carbon\Carbon::parse($expired_at)->getTimestamp() - now()->getTimestamp()) }};
    
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
    let resendSecondsLeft = {{ $next_allowed_at ? max(0, \Carbon\Carbon::parse($next_allowed_at)->getTimestamp() - now()->getTimestamp()) : 0 }};
    
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
                if (el) el.innerHTML = "Kirim ulang tersedia dalam: <strong>" + text + "</strong>";
            }
        }, 1000);
    }
</script>