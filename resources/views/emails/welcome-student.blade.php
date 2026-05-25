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
# Welcome to OliLearn, {{ $user?->name ?? '' }} 👋

We’re excited to have you here.

OliLearn is an **AI-powered learning platform** designed to help you **understand concepts deeply, prepare confidently for exams, and build real knowledge that lasts**—not just cram and forget.

Whether you're studying for an upcoming exam, revising key topics, or learning something new, OliLearn adapts to **how you learn best**.

---

## What You Can Do on OliLearn

- 📚 Learn with **structured courses** built around clear objectives
- 🤖 Ask questions with **Oli Tutor AI**, which answers *only* from your course content
- 📝 Test your understanding with **quizzes and practice assessments**
- 🧠 Create and review **AI-powered flashcards** for faster retention
- 🎯 Follow guided study paths designed for **exam preparation and mastery**

Everything is built to help you move from **confusion → clarity → confidence**.

---

## Getting Started (Takes 2 Minutes)

1. Complete your profile to improve recommendations
2. Browse available courses
3. Start learning and ask questions with Oli Tutor AI
4. Use quizzes and flashcards to track progress
5. Study consistently and watch your understanding grow

---

## Create a Course in One Click

You can enroll for courses for:
- Exam preparation
- School or university subjects
- Professional or skill-based learning
- Personal study goals

**How it works:**
1. Go to your **Catalogs** and search for courses you like
2. Enroll for the course to start
3. Use the Oli tutor and ask questions that relates to topics in the course
4. Enjoy learning and complete each topic before moving ahead.

You can instantly generate:
- Flashcards and learning summaries

So you can focus on learning—not setup.

---

@if(isset($recommendations) && count($recommendations) > 0)
## Recommended for You

Based on your interests, you may like:

@foreach($recommendations as $rec)
- {{ $rec }}
@endforeach

@endif

---

## Start Learning Now

@component('mail::button', ['url' => url('/student/dashboard'), 'color' => 'success'])
Go to Dashboard
@endcomponent

@component('mail::button', ['url' => url('/courses/browse'), 'color' => 'primary'])
Explore Courses
@endcomponent

If you ever get stuck or need guidance, we’re here to help—every step of the way.

Welcome to smarter learning 🚀

    @endcomponent

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
© {{ date('Y') }} OliLearn. All rights reserved.
[Visit Website]({{ config('app.url') }}) | [Contact Support]({{ url('/contact') }}) | [Privacy Policy]({{ url('/privacy') }})
        @endcomponent
    @endslot

@endcomponent
