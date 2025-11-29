{{-- resources/views/emails/course-creation-reminder.blade.php --}}
@component('mail::message')
# {{ $subject }}

Hello {{ $user->name }}!

{{ $message }}

## Why Create Your First Course?

- **Personalized Learning**: Tailor content to your specific needs and goals
- **Track Progress**: Monitor your learning journey with detailed analytics
- **Expert Guidance**: Get support from our Oli AI tutors and educators
- **Flexible Schedule**: Learn at your own pace, anytime, anywhere

@component('mail::button', ['url' => route('student.courses.create'), 'color' => 'success'])
Create Your First Course
@endcomponent

- On your dashboard, click on New Course
- Enter what can be the title of what you will like to  learn
- Select an exam board if it has one, if not skip this.
- Select a subject
- Select your Target Level, the level you will like to be are the end of the course.
- Select a Weekly Study Hours
- Select a Target Completion Date
- You can choose to enter a Description and Learning Objectives if you want to
- Sit back and learn!

@component('mail::button', ['url' => route('courses.index'), 'color' => 'primary'])
Browse Course Examples
@endcomponent

## Need Help Getting Started?

Our team is here to support you! If you have any questions about creating courses or need guidance, don't hesitate to reach out.

@component('mail::panel')
**Quick Tip**: Start with a simple course on a topic you're passionate about. You can always expand it later!
@endcomponent

Happy Learning!<br>
The OliLearn Team

@endcomponent