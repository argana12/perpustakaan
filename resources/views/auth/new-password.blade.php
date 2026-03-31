<form method="POST" action="{{ route('password.update.otp') }}">
@csrf

<input type="password" name="password" placeholder="Password baru">

<input type="password" name="password_confirmation" placeholder="Konfirmasi password">

<button type="submit">Update Password</button>

</form>