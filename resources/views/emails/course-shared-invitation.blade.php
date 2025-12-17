{{-- resources/views/emails/course-shared-invitation.blade.php --}}
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

@component('mail::button', ['url' => route('register') . '?share_token=' . $share->token])
Sign Up to Accept Course
@endcomponent

Once you sign up, the course will be automatically added to your dashboard.

@component('mail::panel')
This invitation will expire in 7 days.
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
