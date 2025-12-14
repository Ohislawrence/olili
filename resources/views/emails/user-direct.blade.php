{{-- resources/views/emails/user-direct.blade.php --}}
@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

# Hello {{ $user->name }}!

{!! $bodyText !!}

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Visit Your Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    @endcomponent
@endslot
@endcomponent
