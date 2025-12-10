<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 12px 12px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 12px 12px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        .info-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #059669;
        }
        .label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        .value {
            color: #6b7280;
        }
        .message-box {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-right: 8px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">New Contact Form Submission</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">From Olilearn Website</p>
        </div>
        
        <div class="content">
            <p style="margin-bottom: 25px;">
                You have received a new contact form submission on the Olilearn website. 
                Here are the details:
            </p>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Full Name</div>
                    <div class="value" style="font-size: 18px; color: #111827;">
                        {{ $data['first_name'] }} {{ $data['last_name'] }}
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="label">Email Address</div>
                    <div class="value">
                        <a href="mailto:{{ $data['email'] }}" style="color: #059669; text-decoration: none;">
                            {{ $data['email'] }}
                        </a>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="label">Phone Number</div>
                    <div class="value">
                        {{ $data['phone'] ?? 'Not provided' }}
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="label">Subject</div>
                    <div class="value">
                        <span class="badge">{{ $data['subject'] }}</span>
                    </div>
                </div>
            </div>
            
            <div style="margin: 25px 0;">
                <div class="label" style="font-size: 16px; margin-bottom: 10px;">Message</div>
                <div class="message-box">
                    {!! nl2br(e($data['message'])) !!}
                </div>
            </div>
            
            <div class="info-item">
                <div class="label">Newsletter Subscription</div>
                <div class="value">
                    @if($data['subscribe_newsletter'])
                        <span style="color: #059669; font-weight: 500;">✓ Subscribed</span>
                    @else
                        <span style="color: #6b7280;">Not subscribed</span>
                    @endif
                </div>
            </div>
            
            <div class="footer">
                <p style="margin: 0;">
                    <strong>Submission Time:</strong> {{ now()->format('F j, Y \a\t g:i A') }}
                </p>
                <p style="margin: 10px 0 0 0;">
                    This email was automatically generated from the Olilearn contact form.
                </p>
            </div>
        </div>
    </div>
</body>
</html>