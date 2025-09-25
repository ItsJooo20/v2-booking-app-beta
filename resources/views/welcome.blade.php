<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Booking Facility') }}</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        
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
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 20px;
            }
            
            .welcome-container {
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
            }
            
            .welcome-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 3rem;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .welcome-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            }
            
            .logo-container {
                width: 100px;
                height: 100px;
                border-radius: 20px;
                background: linear-gradient(135deg, var(--accent), var(--secondary));
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 2rem;
                box-shadow: 0 10px 30px rgba(52, 168, 83, 0.3);
                position: relative;
                overflow: hidden;
            }
            
            .logo-container::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
                transform: rotate(45deg);
                animation: shimmer 3s infinite;
            }
            
            @keyframes shimmer {
                0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
                100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
            }
            
            .welcome-title {
                background: linear-gradient(135deg, var(--dark), var(--accent));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                font-weight: 700;
                font-size: 2.5rem;
                margin-bottom: 1rem;
                text-align: center;
            }
            
            .welcome-subtitle {
                color: var(--text-light);
                font-size: 1.25rem;
                margin-bottom: 2rem;
                text-align: center;
                max-width: 700px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .feature-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: var(--primary);
            }
            
            .feature-title {
                font-weight: 600;
                margin-bottom: 0.75rem;
                color: var(--text);
            }
            
            .feature-description {
                color: var(--text-light);
                font-size: 0.95rem;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, var(--accent), var(--secondary));
                border: none;
                border-radius: 12px;
                padding: 1rem 2rem;
                font-weight: 600;
                font-size: 1.1rem;
                letter-spacing: 0.5px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }
            
            .btn-primary::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s ease;
            }
            
            .btn-primary:hover::before {
                left: 100%;
            }
            
            .btn-primary:hover {
                background: linear-gradient(135deg, var(--success), var(--accent));
                transform: translateY(-3px);
                box-shadow: 0 10px 25px rgba(52, 168, 83, 0.3);
            }
            
            .btn-primary:active {
                transform: translateY(-1px);
            }
            
            .auth-link {
                color: var(--secondary);
                text-decoration: none;
                font-weight: 500;
                transition: all 0.2s ease;
                position: relative;
            }
            
            .auth-link::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 0;
                height: 2px;
                background: var(--primary);
                transition: width 0.3s ease;
            }
            
            .auth-link:hover {
                color: var(--primary);
            }
            
            .auth-link:hover::after {
                width: 100%;
            }
            
            .nav-link {
                color: var(--text-light);
                font-weight: 500;
                transition: all 0.2s ease;
                padding: 0.5rem 1rem;
                border-radius: 8px;
            }
            
            .nav-link:hover {
                color: var(--primary);
                background: rgba(26, 115, 232, 0.1);
            }
            
            .nav-link.active {
                color: var(--primary);
                font-weight: 600;
            }
            
            .btn-outline-primary {
                border: 2px solid var(--primary);
                color: var(--primary);
                font-weight: 500;
                border-radius: 12px;
                padding: 0.75rem 1.5rem;
                transition: all 0.3s ease;
            }
            
            .btn-outline-primary:hover {
                background: var(--primary);
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(26, 115, 232, 0.2);
            }
            
            @media (max-width: 768px) {
                .welcome-card {
                    padding: 2rem 1.5rem;
                }
                
                .welcome-title {
                    font-size: 2rem;
                }
                
                .welcome-subtitle {
                    font-size: 1.1rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="welcome-container">
            <div class="welcome-card">
                <div class="text-center mb-5">
                    <div class="logo-container">
                        <i class="bi bi-calendar-check text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <h1 class="welcome-title">Facility Reservation System</h1>
                    <p class="welcome-subtitle">
                        Easily book and manage facility reservations with our simple and intuitive platform. 
                        Get started today and streamline your booking process.
                    </p>
                </div>
                
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="bi bi-calendar2-week feature-icon"></i>
                            <h3 class="feature-title">Easy Booking</h3>
                            <p class="feature-description">
                                Quickly reserve facilities with our user-friendly interface and real-time availability.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="bi bi-clock-history feature-icon"></i>
                            <h3 class="feature-title">Manage Reservations</h3>
                            <p class="feature-description">
                                View, modify, or cancel your reservations anytime from your dashboard.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="bi bi-bell feature-icon"></i>
                            <h3 class="feature-title">Notifications</h3>
                            <p class="feature-description">
                                Get timely reminders and updates about your upcoming reservations.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    @if (Route::has('login'))
                        <div class="d-flex justify-content-center gap-3 mb-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                    <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Log In
                                </a>
                                
                                {{-- @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-person-plus me-2"></i> Register
                                    </a>
                                @endif --}}
                            @endauth
                        </div>
                    @endif
                    
                    {{-- <p class="text-muted mb-0">
                        Need help? <a href="#" class="auth-link">Contact support</a>
                    </p> --}}
                </div>
            </div>
        </div>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>