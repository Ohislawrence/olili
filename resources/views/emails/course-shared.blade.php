{{-- resources/views/emails/course-shared.blade.php --}}
@component('mail::message')
# Course Shared with You!

**{{ $sharer->name }}** has shared the course **"{{ $course->title }}"** with you.

@if($message)
**Message from {{ $sharer->name }}:**
"{{ $message }}"
@endif

## About the Course
- **Subject:** {{ $course->subject }}
- **Level:** {{ $course->level }}
- **Estimated Duration:** {{ $course->estimated_duration_hours }} hours

@component('mail::button', ['url' => route('course.share.accept', $share->token)])
View & Accept Course
@endcomponent

Once accepted, the course will be added to your dashboard.

@component('mail::panel')
This invitation will expire in 7 days.
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
