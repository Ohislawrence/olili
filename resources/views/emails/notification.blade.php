{{-- resources/views/emails/notification.blade.php --}}
@component('mail::layout')

    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ $message->embed(public_path('logo-olilearn.PNG')) }}"
                 alt="{{ config('app.name') }}"
                 height="50">
        @endcomponent
    @endslot

    {{-- Body --}}
    @component('mail::message')

# {{ $greeting ?? 'Hello!' }}

@foreach($introLines as $line)
{{ $line }}

@endforeach

@isset($actionText)
@component('mail::panel')
@component('mail::button', ['url' => $actionUrl, 'color' => 'success'])
{{ $actionText }}
@endcomponent
@endcomponent
@endisset

@foreach($outroLines as $line)
{{ $line }}

@endforeach

@isset($salutation)
{{ $salutation }}
@else
Regards,<br>
{{ config('app.name') }}
@endisset


@if(isset($actionUrl))
@slot('subcopy')
@component('mail::subcopy')
If you're having trouble clicking the **"{{ $actionText }}"** button,  
copy and paste the URL below into your browser:

**{{ $actionUrl }}**
@endcomponent
@endslot
@endif

    @endcomponent

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.  
[Visit Website]({{ config('app.url') }}) •  
[Support]({{ config('app.url') }}/contact) •  
[Privacy]({{ config('app.url') }}/privacy)
        @endcomponent
    @endslot

@endcomponent
