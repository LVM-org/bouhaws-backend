<x-mail::message>
    Hi {{ $user->name }},

    We've received a request to reset the password for your {{ config('app.name') }} account. To verify your identity,
    please use the following 4-digit code:

    ## {{ $user->otp }}

    This code will expire in 30 minutes, so please use it promptly.

    If you did not initiate this request or believe it is in error, please ignore this email. Your account security is
    important to us, and no changes will be made without your confirmation.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>