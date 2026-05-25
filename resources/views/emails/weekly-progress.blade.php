{{-- resources/views/emails/weekly-progress.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .course-card { background: white; border-left: 4px solid #667eea; padding: 15px; margin-bottom: 15px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .progress-bar { background: #e0e0e0; border-radius: 10px; height: 10px; margin: 10px 0; }
        .progress-fill { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; border-radius: 10px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .suggested-course { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Week {{ $weekNumber }} Learning Update</h1>
            <p>Hello {{ $user->name }}, ready for another productive week?</p>
        </div>

        <div class="content">
            @if($enrolledCourses->count() > 0)
                <h2>📖 Your Current Courses</h2>
                <p>Keep up the momentum! Here are your active courses:</p>

                @foreach($enrolledCourses as $course)
                    <div class="course-card">
                        <h3 style="margin-top: 0;">{{ $course['title'] }}</h3>
                        <p><strong>Progress:</strong> {{ number_format($course['progress'], 1) }}%</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $course['progress'] }}%"></div>
                        </div>
                        <p><strong>Status:</strong> {{ ucfirst($course['status']) }}</p>
                        <a href="{{ $course['course_url'] }}" class="btn">Continue Learning →</a>
                    </div>
                @endforeach
            @endif

            @if($suggestedCourses->count() > 0)
                <h2>🌟 Suggested Courses for You</h2>
                <p>Expand your knowledge with these courses:</p>

                @foreach($suggestedCourses as $course)
                    <div class="suggested-course">
                        <h4 style="margin-top: 0;">{{ $course['title'] }}</h4>
                        <p><strong>Subject:</strong> {{ $course['subject'] }} • <strong>Level:</strong> {{ $course['level'] }}</p>
                        <p>{{ $course['description'] }}</p>
                        <p><strong>Duration:</strong> {{ $course['estimated_duration'] }} hours • <strong>Students:</strong> {{ $course['enrolled_count'] }}</p>
                        <div>
                            <a href="{{ $course['course_url'] }}" style="color: #667eea; margin-right: 15px;">View Details</a>
                            <a href="{{ $course['enroll_url'] }}" class="btn">Enroll Now</a>
                        </div>
                    </div>
                @endforeach
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('dashboard') }}" class="btn">Go to Dashboard</a>
            </div>

            <div class="footer">
                <p>This is an automated weekly update from {{ config('app.name') }}.</p>
                <p>To adjust your email preferences, visit your <a href="{{ $user->hasRole('student') ? route('student.profile.edit') : route('dashboard') }}">account settings</a>.</p>
                <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
