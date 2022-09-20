@component('mail::message')
@if(!empty($description))

{{$description}} : {{$user->otp}}

@else

Your One Time Password is: {{$user->otp}}

@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
