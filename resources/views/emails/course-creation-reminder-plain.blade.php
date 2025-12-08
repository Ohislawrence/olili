{{-- resources/views/emails/course-creation-reminder-plain.blade.php --}}
{{ $subject }}

Hello {{ $user->name ?? 'there' }}!

{{ $message ?? '' }}

WHY CREATE YOUR FIRST COURSE?
- Personalized Learning: Tailor content to your specific needs and goals
- Track Progress: Monitor your learning journey with detailed analytics
- Expert Guidance: Get support from our AI tutors and educators
- Flexible Schedule: Learn at your own pace, anytime, anywhere

CREATE YOUR COURSE:
{{ route('student.courses.create') }}

BROWSE EXAMPLES:
{{ route('student.courses.index') }}

Need Help Getting Started?
Our team is here to support you! If you have any questions about creating courses or need guidance, don't hesitate to reach out.

Quick Tip: Start with a simple course on a topic you're passionate about. You can always expand it later!

Happy Learning!
The OliLearn Team
