{{-- resources/views/emails/user-direct.blade.php --}}
@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

{{-- Variables have already been replaced by the Notification class --}}
# {{ $subject }}

{!! $bodyText !!}

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Visit Your Dashboard
@endcomponent

<p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
    This message was sent specifically to you, {{ $user->name }}.
</p>

Warm regards,<br>
{{ config('app.name') }}

@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    @endcomponent
@endslot
@endcomponent
