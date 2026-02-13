{{-- resources/views/emails/user-direct.blade.php --}}
@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

{{-- Subject is displayed in email clients, so we don't need to repeat it --}}
{{-- # {{ $subject }} --}} {{-- Remove this line as subject is already in email header --}}

{{-- Render the message content - it might contain HTML from the editor --}}
<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #374151;">
    {!! $bodyText !!}
</div>

{{-- Only show dashboard button if not already in the message --}}
@if(!str_contains($bodyText, config('app.url')))
    @component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
        Visit Your Dashboard
    @endcomponent
@endif

{{-- Personalization footer --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
    <tr>
        <td style="color: #6b7280; font-size: 14px;">
            <p style="margin: 0 0 10px 0;">
                This message was sent specifically to
                <strong>{{ $user->name }}</strong> ({{ $user->email }})
            </p>

            {{-- Show user role if available --}}
            @if($user->roles && $user->roles->isNotEmpty())
                <p style="margin: 0 0 10px 0; color: #9ca3af; font-size: 13px;">
                    Role: {{ $user->roles->first()->name }}
                </p>
            @endif

            <p style="margin: 0 0 5px 0;">
                Warm regards,<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </td>
    </tr>
</table>

{{-- Optional unsubscribe link --}}
{{--
@if(isset($showUnsubscribe) && $showUnsubscribe)
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
        <tr>
            <td align="center" style="color: #9ca3af; font-size: 12px;">
                <a href="{{ route('user.preferences', $user->id) }}" style="color: #6b7280; text-decoration: underline;">
                    Manage email preferences
                </a>
            </td>
        </tr>
    </table>
@endif
--}}

@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        <br>
        <small style="color: #9ca3af;">{{ config('app.url') }}</small>
    @endcomponent
@endslot
@endcomponent
