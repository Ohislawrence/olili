<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .email-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            padding: 30px 40px;
            text-align: center;
        }

        .logo {
            max-height: 50px;
            width: auto;
        }

        .email-content {
            padding: 40px;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #059669;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }

        .button:hover {
            background-color: #047857;
        }

        .greeting {
            color: #059669;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .content-block {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .footer-text {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            color: #059669;
            text-decoration: none;
            margin: 0 10px;
        }

        @media (max-width: 600px) {
            .email-content {
                padding: 20px;
            }

            .email-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Logo -->
        <div class="email-header">
            <a href="{{ config('app.url') }}">
                <img src="{{ $message->embed(public_path('logo-olilearn.PNG')) }}" alt="{{ config('app.name') }}" class="logo">
            </a>
        </div>

        <!-- Email Content -->
        <div class="email-content">
            @if(isset($greeting) && $greeting)
                <h1 class="greeting">{{ $greeting }}</h1>
            @endif

            @foreach($introLines as $line)
                <p>{{ $line }}</p>
            @endforeach

            @isset($actionText)
                <div class="content-block">
                    @isset($actionUrl)
                        <a href="{{ $actionUrl }}" class="button" target="_blank">
                            {{ $actionText }}
                        </a>
                    @endisset
                </div>
            @endisset

            @foreach($outroLines as $line)
                <p>{{ $line }}</p>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <p>
                    <a href="{{ config('app.url') }}" style="color: #059669; text-decoration: none;">Visit our website</a> |
                    <a href="{{ config('app.url') }}/contact" style="color: #059669; text-decoration: none;">Contact Support</a>
                </p>

                <div class="social-links">
                    <a href="#">Facebook</a> •
                    <a href="#">Twitter</a> •
                    <a href="#">LinkedIn</a>
                </div>

                <p style="font-size: 12px; color: #9ca3af; margin-top: 20px;">
                    If you're having trouble clicking the "{{ $actionText ?? '' }}" button,
                    copy and paste the URL below into your web browser:<br>
                    <span style="color: #059669;">{{ $actionUrl ?? '' }}</span>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
