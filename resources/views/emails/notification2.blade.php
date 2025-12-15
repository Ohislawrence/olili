<!-- resources/views/emails/notification.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        .notification-container {
            max-width: 600px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }
        .notification-header {
            background: {{ $typeColor }};
            color: white;
            padding: 20px;
            text-align: center;
        }
        .notification-body {
            padding: 30px;
            background: #f9f9f9;
        }
        .notification-footer {
            padding: 20px;
            background: #f0f0f0;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="notification-container">
        <div class="notification-header">
            <h1>{{ $title }}</h1>
        </div>
        <div class="notification-body">
            <p>{{ $message }}</p>

            @if(isset($actionUrl))
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ $actionUrl }}" style="background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    View Details
                </a>
            </div>
            @endif
        </div>
        <div class="notification-footer">
            <p>You received this email because you're a registered user of {{ config('app.name') }}</p>
            <p>
                <a href="{{ route('notifications.index') }}">Manage notifications</a> |
                <a href="{{ route('profile.notifications') }}">Notification settings</a>
            </p>
        </div>
    </div>
</body>
</html>
