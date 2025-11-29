{{-- resources/views/emails/welcome-student.blade.php --}}
@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ $message->embed(public_path('logo-olilearn.PNG')) }}"
                 alt="OliLearn"
                 style="max-height: 50px; width: auto;">
        @endcomponent
    @endslot

    {{-- Body --}}
    <h1 style="color: #333; font-size: 24px; margin-bottom: 20px;">Welcome to OliLearn, {{ $user->name }}! 🎉</h1>

    <p style="color: #666; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        We're thrilled to have you join our learning community. Your educational journey starts now!
    </p>

    <h2 style="color: #333; font-size: 20px; margin-bottom: 15px;">Getting Started</h2>

    <ul style="color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
        <li style="margin-bottom: 8px;"><strong>Complete your profile</strong> to get personalized recommendations</li>
        <li style="margin-bottom: 8px;"><strong>Explore courses</strong> that match your interests</li>
        <li style="margin-bottom: 8px;"><strong>Set your learning goals</strong> to track your progress</li>
        <li style="margin-bottom: 8px;"><strong>Connect with tutors</strong> who can guide your learning</li>
    </ul>

    {{-- Safe check for recommendations --}}
    @if(isset($recommendations) && count($recommendations) > 0)
    <h2 style="color: #333; font-size: 20px; margin-bottom: 15px;">Personalized Recommendations</h2>
    <p style="color: #666; font-size: 16px; margin-bottom: 15px;">
        Based on your profile, we recommend:
    </p>
    <ul style="color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
        @foreach($recommendations as $recommendation)
        <li style="margin-bottom: 8px;">{{ $recommendation }}</li>
        @endforeach
    </ul>
    @endif

    <h2 style="color: #333; font-size: 20px; margin-bottom: 15px;">Quick Links</h2>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" style="border-radius: 5px; background-color: #28a745;">
                            <a href="{{ url('/dashboard') }}" 
                               style="display: inline-block; padding: 12px 30px; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                Go to Dashboard
                            </a>
                        </td>
                        <td width="20"></td>
                        <td align="center" style="border-radius: 5px; background-color: #007bff;">
                            <a href="{{ url('/courses') }}" 
                               style="display: inline-block; padding: 12px 30px; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                Browse Courses
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="color: #666; font-size: 16px; line-height: 1.5;">
        Need help getting started? Our support team is here to assist you.
    </p>

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <p style="color: #999; font-size: 14px; text-align: center;">
                © {{ date('Y') }} OliLearn. All rights reserved.<br>
                <a href="{{ config('app.url') }}" style="color: #999; text-decoration: none;">Visit Website</a> |
                <a href="{{ url('/contact') }}" style="color: #999; text-decoration: none;">Contact Support</a> |
                <a href="{{ url('/privacy') }}" style="color: #999; text-decoration: none;">Privacy Policy</a>
            </p>
        @endcomponent
    @endslot
@endcomponent