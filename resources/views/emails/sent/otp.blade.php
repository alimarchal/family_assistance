@component('mail::message')
# One Time Password (OTP)

Your One Time Password is: {{$user->otp}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
