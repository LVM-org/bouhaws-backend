<x-mail::message>
    Hi {{ $user->name }},

    Welcome to {{ config('app.name') }}! To secure your account, please verify your email by using this 4-digit code:

    <h2>{{ $user->otp }}</h2>

    This code will expire in 30 minutes, so please use it promptly.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>