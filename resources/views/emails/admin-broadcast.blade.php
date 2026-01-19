{{-- resources/views/emails/admin-broadcast.blade.php --}}
@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

@php
    // Replace variables in subject
    $subject = str_replace(
        ['{{name}}', '{{email}}', '{{role}}', '{{app_name}}', '{{app_url}}', '{{year}}'],
        [$user->name, $user->email, $user->roles->first()?->name ?? 'User', config('app.name'), config('app.url'), date('Y')],
        $subject
    );

    // Replace variables in message
    $message = str_replace(
        ['{{name}}', '{{email}}', '{{role}}', '{{app_name}}', '{{app_url}}', '{{year}}'],
        [$user->name, $user->email, $user->roles->first()?->name ?? 'User', config('app.name'), config('app.url'), date('Y')],
        $message
    );
@endphp

# {!! $subject !!}

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
