{{-- resources/views/emails/welcome-student.blade.php --}}
@component('mail::layout')

    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ $message->embed(public_path('logo-olilearn.PNG')) }}" alt="OliLearn" height="50">
        @endcomponent
    @endslot

    {{-- Body --}}
    @component('mail::message')
# Welcome to OliLearn, {{ $user->name }}! 🎉

We're excited to have you on board. Your learning journey begins now, and we’ve built OliLearn to help you learn smarter with AI-assisted courses, structured study paths, quizzes, and flashcards.

---

## Getting Started

- Complete your profile for better recommendations  
- Explore available courses or create your own  
- Use **Oli Tutor AI** to ask topic-related questions  
- Take quizzes to measure understanding  
- Use flashcards to master key concepts  

---

## How to Create Your First Course

Creating a course on OliLearn is simple—whether it’s for WAEC, JAMB, NECO, university courses, professional exams, or any personal topic.

1. Go to your **Dashboard** and click **“New Course”**.  
2. Enter a course title (e.g., *JAMB Chemistry*, *Python Basics*, *Financial Accounting*).  
3. Or choose from structured **Exam Boards**.  
4. Set Target Completion Date, Level, and Weekly Study Hours.  
5. *(Optional)* Add learning objectives and a description.  
6. Click **Create Course** to begin.  

Once submitted, OliLearn automatically generates all topics, modules, quizzes, and flashcards for you.

---

@if(isset($recommendations) && count($recommendations) > 0)
## Personalized Course Suggestions

Based on your profile, you may enjoy:

@foreach($recommendations as $rec)
- {{ $rec }}
@endforeach

@endif

---

## Quick Links

@component('mail::button', ['url' => url('/dashboard'), 'color' => 'success'])
Go to Dashboard
@endcomponent

@component('mail::button', ['url' => url('/courses'), 'color' => 'primary'])
Browse Courses
@endcomponent


If you ever need help, our support team is always available.

    @endcomponent

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
© {{ date('Y') }} OliLearn. All rights reserved.  
[Visit Website]({{ config('app.url') }}) | [Contact Support]({{ url('/contact') }}) | [Privacy Policy]({{ url('/privacy') }})
        @endcomponent
    @endslot

@endcomponent
