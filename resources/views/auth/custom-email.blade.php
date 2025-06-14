<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email Address</title>
    <style>
        :root {
            --primary: #1A73E8;      
            --secondary: #4285F4;    
            --light: #E8F0FE;      
            --dark: #0D47A1;         
            --accent: #34A853;       
            --hover: #2B7DE9;        
            --text: #202124;        
            --text-light: #5F6368;   
            --border: #DADCE0;
            --danger: #EA4335;
            --success: #34A853;
            --warning: #FBBC04;      
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text);
            line-height: 1.6;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 500px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .email-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .header {
            text-align: left;
            margin-bottom: 2rem;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: #34A853;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0 2rem 0;
            box-shadow: 0 8px 20px rgba(52, 168, 83, 0.3);
        }
        
        .title {
            background: linear-gradient(135deg, var(--dark), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 2rem;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.02em;
        }
        
        .subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        
        .content {
            margin-bottom: 2rem;
        }
        
        .greeting {
            color: var(--text);
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .message {
            color: var(--text-light);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .button-container {
            text-align: center;
            margin: 2rem 0;
        }
        
        .btn-primary {
            background: #1A73E8;
            border: none;
            border-radius: 12px;
            color: white !important;
            text-decoration: none;
            display: inline-block;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #2B7DE9;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 115, 232, 0.3);
            color: white !important;
            text-decoration: none;
        }
        
        .verification-link {
            background: rgba(232, 240, 254, 0.5);
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
            text-align: center;
        }
        
        .verification-link a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            word-break: break-all;
            font-weight: 500;
        }
        
        .small-text {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-top: 1.5rem;
        }
        
        .footer {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(218, 220, 224, 0.3);
            margin-top: 2rem;
        }
        
        .footer p {
            color: var(--text-light);
            font-size: 0.8rem;
            margin: 0.25rem 0;
        }
        
        @media (max-width: 576px) {
            body {
                padding: 20px 10px;
            }
            
            .email-container {
                padding: 2rem 1.5rem;
                margin: 1rem 0;
            }
            
            .title {
                font-size: 1.75rem;
            }
            
            .btn-primary {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-container">
                <span style="color: white; font-size: 2rem;">✓</span>
            </div>
            <h1 class="title">Verify Your Email</h1>
            <p class="subtitle">One more step to complete your registration</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $user->name }},</p>
            <p class="message">Thank you for registering with {{ config('app.name') }}. Please verify your email address by clicking the button below:</p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="btn-primary">Verify Email Address</a>
            </div>
            
            <p class="message">Or copy and paste this verification link into your browser:</p>
            <div class="verification-link">
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>
            
            <p class="small-text">If you did not create an account, no further action is required.</p>
            <p class="small-text">This verification link will expire in {{ config('auth.verification.expire', 60) }} minutes.</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>If you're having trouble clicking the button, copy and paste the URL above into your web browser.</p>
        </div>
    </div>
</body>
</html>