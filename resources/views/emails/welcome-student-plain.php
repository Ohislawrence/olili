Welcome to OliLearn, {{ $user->name }}! 🎉

We're thrilled to have you join our learning community. Your educational journey starts now!

GETTING STARTED:
- Complete your profile to get personalized recommendations
- Explore courses that match your interests  
- Set your learning goals to track your progress
- Connect with tutors who can guide your learning

QUICK LINKS:
Dashboard: {{ url('/dashboard') }}
Browse Courses: {{ url('/courses') }}

Need help getting started? Our support team is here to assist you.

---
© {{ date('Y') }} OliLearn. All rights reserved.
Website: {{ config('app.url') }}
Contact: {{ url('/contact') }}
Privacy Policy: {{ url('/privacy') }}