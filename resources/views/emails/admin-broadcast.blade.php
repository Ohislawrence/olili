{{-- resources/views/emails/admin-broadcast.blade.php --}}
@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

# {{ $subject ?? 'Notification' }}

{!! $message !!}

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Visit Platform
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    @endcomponent
@endslot
@endcomponent