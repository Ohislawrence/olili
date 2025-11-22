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
    # Welcome to OliLearn, {{ $user->name }}! 🎉

    We're thrilled to have you join our learning community. Your educational journey starts now!

    ## Getting Started

    - **Complete your profile** to get personalized recommendations
    - **Explore courses** that match your interests
    - **Set your learning goals** to track your progress
    - **Connect with tutors** who can guide your learning

    {{-- Safe check for recommendations --}}
    @if(isset($recommendations) && count($recommendations) > 0)
    ## Personalized Recommendations

    Based on your profile, we recommend:

    @foreach($recommendations as $recommendation)
    - {{ $recommendation }}
    @endforeach
    @endif

    ## Quick Links

    @component('mail::button', ['url' => url('/dashboard'), 'color' => 'success'])
        Go to Dashboard
    @endcomponent

    @component('mail::button', ['url' => url('/courses'), 'color' => 'primary'])
        Browse Courses
    @endcomponent

    Need help getting started? Our support team is here to assist you.

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} OliLearn. All rights reserved.
            [Visit Website]({{ config('app.url') }}) |
            [Contact Support]({{ url('/contact') }}) |
            [Privacy Policy]({{ url('/privacy') }})
        @endcomponent
    @endslot
@endcomponent
