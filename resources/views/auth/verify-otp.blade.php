<form method="POST" action="{{ route('otp.verify') }}">
@csrf

<input type="text" name="otp" placeholder="Masukkan OTP">

<button type="submit">Verifikasi OTP</button>

</form>