{{-- resources/views/emails/welcome-student.blade.php --}}
@component('mail::layout')

    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ $message->embed(public_path('logo-olilearn.PNG')) }}"
                 alt="OliLearn"
                 style="max-height:50px;width:auto;">
        @endcomponent
    @endslot

    {{-- Body --}}
    @component('mail::message')
        <h1 style="color:#333;font-size:24px;margin-bottom:20px;">
            Welcome to OliLearn, {{ $user->name }}! 🎉
        </h1>

        <p style="color:#666;font-size:16px;line-height:1.5;margin-bottom:20px;">
            We're excited to have you on board. Your learning journey begins now, and we’ve built OliLearn to help you learn smarter—with AI assistance, structured courses, quizzes, flashcards, and much more.
        </p>

        <h2 style="color:#333;font-size:20px;margin-bottom:15px;">
            Getting Started
        </h2>

        <ul style="color:#666;font-size:16px;line-height:1.6;margin-bottom:25px;">
            <li style="margin-bottom:8px;">Complete your profile for better course recommendations</li>
            <li style="margin-bottom:8px;">Explore available courses or create your own learning path</li>
            <li style="margin-bottom:8px;">Use Oli Tutor (AI) to ask questions related to your topics</li>
            <li style="margin-bottom:8px;">Take quizzes to track your understanding</li>
            <li style="margin-bottom:8px;">Use flashcards to retain key concepts</li>
        </ul>

        {{-- Course creation guide --}}
        <h2 style="color:#333;font-size:20px;margin-bottom:15px;">
            How to Create Your First Course
        </h2>

        <p style="color:#666;font-size:16px;line-height:1.5;margin-bottom:15px;">
            Creating a course on OliLearn is simple—whether it’s a WAEC subject, JAMB topic, professional certification, or any custom topic you want to study.
        </p>

        <ol style="color:#666;font-size:16px;line-height:1.6;margin-bottom:25px;padding-left:18px;">
            <li style="margin-bottom:10px;">
               Go to your <strong>Dashboard</strong> and click <strong>“New Course”</strong>.
            </li>
            <li style="margin-bottom:10px;">
               Enter your course title (e.g., “JAMB Chemistry”, “Python Basics”, “Financial Accounting”).
            </li>
            <li style="margin-bottom:10px;">
               Or use Exam Boards to structure your learning path.
            </li>
            <li style="margin-bottom:10px;">
               Enter Target Completion Date, Target Level and Weekly Study Hour.
            </li>
            <li style="margin-bottom:10px;">
               You can also add Description and Learning Objectives if you want to.
            </li>
            <li style="margin-bottom:10px;">
               Click on Create Course to start.
            </li>
        </ol>

        {{-- Optional personalized recommendations --}}
        @if(isset($recommendations) && count($recommendations) > 0)
            <h2 style="color:#333;font-size:20px;margin-bottom:15px;">
                Personalized Course Suggestions
            </h2>

            <p style="color:#666;font-size:16px;margin-bottom:15px;">
                Based on your profile, you may enjoy:
            </p>

            <ul style="color:#666;font-size:16px;line-height:1.6;margin-bottom:25px;">
                @foreach($recommendations as $rec)
                    <li style="margin-bottom:8px;">{{ $rec }}</li>
                @endforeach
            </ul>
        @endif

        <h2 style="color:#333;font-size:20px;margin-bottom:15px;">
            Quick Links
        </h2>

        @component('mail::button', ['url' => url('/dashboard'), 'color' => 'success'])
            Go to Dashboard
        @endcomponent

        @component('mail::button', ['url' => url('/courses'), 'color' => 'primary'])
            Browse Courses
        @endcomponent

        <p style="color:#666;font-size:16px;line-height:1.5;margin-top:25px;">
            If you need help at any point, our support team is always here for you.
        </p>

    @endcomponent

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <p style="color:#999;font-size:14px;text-align:center;">
                © {{ date('Y') }} OliLearn. All rights reserved.<br>
                <a href="{{ config('app.url') }}" style="color:#999;text-decoration:none;">Visit Website</a> |
                <a href="{{ url('/contact') }}" style="color:#999;text-decoration:none;">Contact Support</a> |
                <a href="{{ url('/privacy') }}" style="color:#999;text-decoration:none;">Privacy Policy</a>
            </p>
        @endcomponent
    @endslot

@endcomponent
