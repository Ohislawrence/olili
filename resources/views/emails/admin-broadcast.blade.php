{{-- resources/views/emails/admin-broadcast.blade.php --}}
@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot



{!! $message !!}

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Visit Your Dashboard
@endcomponent

Warm regards,<br>
{{ config('app.name') }}

@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
        <small>You're receiving this email as a {{ $user->roles->first()?->name ?? 'user' }} of Olilearn.</small>
    @endcomponent
@endslot
@endcomponent
