{{-- resources/views/emails/inactivity-reminder.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
            border-radius: 0 0 20px 20px;
        }
        .content {
            background: #ffffff;
            padding: 40px 20px;
            border-radius: 20px;
            margin-top: -20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .course-card {
            background: linear-gradient(135deg, #f6f9fc 0%, #edf2f7 100%);
            border-left: 4px solid #667eea;
            padding: 25px;
            margin: 25px 0;
            border-radius: 10px;
        }
        .progress-container {
            margin: 25px 0;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
            color: #4a5568;
        }
        .progress-bar {
            background: #e2e8f0;
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .milestone {
            background: #ebf8ff;
            border: 2px solid #4299e1;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .action-list {
            background: #f7fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .action-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .action-icon {
            background: #4299e1;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 12px;
        }
        .urgency-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        .urgency-high { background: #fed7d7; color: #9b2c2c; }
        .urgency-medium { background: #feebc8; color: #9c4221; }
        .urgency-low { background: #c6f6d5; color: #276749; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 5px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            background: #edf2f7;
            color: #4a5568;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #718096;
            font-size: 13px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .stat-box {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="content">
            <h2 style="color: #2d3748; margin-top: 0;">Hi {{ $user->name }},</h2>

            <p>We noticed you haven't made progress on <strong>{{ $course->title }}</strong> in a while. Let's get you back on track!</p>

            <div class="course-card">
                <h3 style="color: #2d3748; margin-top: 0; font-size: 20px;">
                    {{ $course->title }}
                    @if($course->subject)
                        <span style="font-size: 14px; color: #718096;">• {{ $course->subject->name ?? 'General' }}</span>
                    @endif
                </h3>

                <div class="progress-container">
                    <div class="progress-label">
                        <span>Your Progress</span>
                        <span><strong>{{ number_format($progressPercentage, 1) }}%</strong></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>

                @if($dueDateInfo)
                    <div style="margin: 15px 0;">
                        <strong>Due Date:</strong>
                        {{ $dueDateInfo['message'] }}
                        <span class="urgency-badge urgency-{{ $dueDateInfo['urgency'] }}">
                            {{ ucfirst($dueDateInfo['status']) }}
                        </span>
                    </div>
                @endif
            </div>

            @if($nextMilestone)
                <div class="milestone">
                    <h4 style="margin-top: 0; color: #2b6cb0;">🎯 Next Milestone</h4>
                    <p style="margin: 10px 0;">You're only <strong>{{ $nextMilestone['remaining'] }}%</strong> away from reaching <strong>{{ $nextMilestone['text'] }}</strong>!</p>
                    @if($nextMilestone['percentage'] == 100)
                        <p style="margin: 10px 0; font-weight: 600;">Complete the course and earn your certificate! 🎓</p>
                    @endif
                </div>
            @endif

            <h3 style="color: #2d3748;">🚀 Quick Actions to Get Started</h3>

            <div class="action-list">
                @foreach($suggestedActions as $index => $action)
                    <div class="action-item">
                        <div class="action-icon">{{ $index + 1 }}</div>
                        <span>{{ $action }}</span>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: center; flex-wrap: wrap; margin: 30px 0;">
                <a href="{{ route('student.courses.learn', $course->id) }}" class="btn">Resume Course →</a>
                <a href="{{ route('student.flashcards.index') }}" class="btn btn-secondary">Create Flashcards</a>
                <a href="{{ route('student.exam-preps.index') }}" class="btn btn-secondary">Exam Prep Quiz</a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 30px 0;">
                <div class="stat-box">
                    <div class="stat-number">{{ $daysInactive }}</div>
                    <div class="stat-label">Days Inactive</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ number_format($progressPercentage, 0) }}%</div>
                    <div class="stat-label">Overall Progress</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ $course->modules()->count() }}</div>
                    <div class="stat-label">Total Modules</div>
                </div>
            </div>

            <div class="footer">
                <p style="margin-bottom: 5px;">
                    This is an automated reminder from {{ config('app.name') }}.
                    <br>We're here to help you succeed in your learning journey!
                </p>
                <p style="margin: 10px 0;">
                    <a href="{{ route('student.notifications.index') }}" style="color: #667eea; text-decoration: none;">Notification</a> |
                    <a href="{{ route('student.catalog.browse') }}" style="color: #667eea; text-decoration: none;">Browse Courses</a> |
                    <a href="{{ route('student.dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
                </p>
                <p style="font-size: 12px; color: #a0aec0; margin-top: 20px;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                    {{ config('app.address') ?? 'Online Learning Platform' }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
