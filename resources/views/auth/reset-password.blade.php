<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Booking Facility') }} - Reset Password</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
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
        
        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: auto;
        }
        
        .form-control:focus, .form-select:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(26, 115, 232, 0.1);
            transform: translateY(-2px);
        }
        
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 4px rgba(234, 67, 53, 0.1);
        }
        
        .form-control.is-valid, .form-select.is-valid {
            border-color: var(--success);
            box-shadow: 0 0 0 4px rgba(52, 168, 83, 0.1);
        }
        
        .form-floating > label {
            color: var(--text-light);
            font-weight: 500;
            padding: 1rem 1.25rem;
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
        
        .invalid-feedback {
            color: var(--danger);
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .valid-feedback {
            color: var(--success);
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-success {
            background: rgba(52, 168, 83, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        .alert-danger {
            background: rgba(234, 67, 53, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .title {
            background: linear-gradient(135deg, var(--dark), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: color 0.2s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--primary);
        }
        
        .password-strength {
            margin-top: 0.5rem;
            padding: 0.75rem;
            border-radius: 8px;
            background: rgba(248, 249, 250, 0.8);
            font-size: 0.875rem;
        }
        
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: var(--border);
            margin: 0.5rem 0;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease, background-color 0.3s ease;
            width: 0%;
        }
        
        .strength-weak { background-color: var(--danger); }
        .strength-medium { background-color: var(--warning); }
        .strength-strong { background-color: var(--success); }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        @media (max-width: 576px) {
            .auth-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .title {
                font-size: 1.75rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center">
                <div class="logo-container">
                    <i class="bi bi-shield-lock text-white" style="font-size: 2rem;"></i>
                </div>
                <h1 class="title">Reset Password</h1>
                <p class="subtitle">Create a new password for your account</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Please check the following errors:
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" novalidate>
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="form-floating">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $request->email) }}" 
                           placeholder="Enter your email"
                           required 
                           autofocus 
                           autocomplete="username">
                    <label for="email">Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-floating">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Create new password"
                           required 
                           autocomplete="new-password">
                    <label for="password">New Password</label>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="password-strength">
                        <div>Password Strength: <span id="strength-text">Weak</span></div>
                        <div class="strength-bar">
                            <div class="strength-fill strength-weak" id="strength-bar"></div>
                        </div>
                        <small class="text-muted">Use at least 8 characters with a mix of letters, numbers & symbols</small>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-floating">
                    <input type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           placeholder="Confirm new password"
                           required 
                           autocomplete="new-password">
                    <label for="password_confirmation">Confirm New Password</label>
                    <button type="button" class="password-toggle" id="toggleConfirmPassword">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat me-2"></i> Reset Password
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="mb-0">Remember your password? 
                        <a href="{{ route('login') }}" class="auth-link">Sign in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password toggle functionality
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const password = document.querySelector('#password');
        const confirmPassword = document.querySelector('#password_confirmation');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });

        // Password strength checker
        password.addEventListener('input', function() {
            const strengthBar = document.querySelector('#strength-bar');
            const strengthText = document.querySelector('#strength-text');
            const passwordValue = this.value;
            
            // Reset
            strengthBar.style.width = '0%';
            strengthBar.className = 'strength-fill';
            
            if (passwordValue.length === 0) {
                strengthText.textContent = '';
                return;
            }
            
            // Calculate strength
            let strength = 0;
            
            // Length
            if (passwordValue.length >= 8) strength += 1;
            if (passwordValue.length >= 12) strength += 1;
            
            // Contains numbers
            if (/\d/.test(passwordValue)) strength += 1;
            
            // Contains special chars
            if (/[!@#$%^&*(),.?":{}|<>]/.test(passwordValue)) strength += 1;
            
            // Contains both lower and upper case
            if (/[a-z]/.test(passwordValue) && /[A-Z]/.test(passwordValue)) strength += 1;
            
            // Update UI
            let width = 0;
            let className = '';
            let text = '';
            
            if (strength <= 2) {
                width = 33;
                className = 'strength-weak';
                text = 'Weak';
            } else if (strength <= 4) {
                width = 66;
                className = 'strength-medium';
                text = 'Medium';
            } else {
                width = 100;
                className = 'strength-strong';
                text = 'Strong';
            }
            
            strengthBar.style.width = width + '%';
            strengthBar.classList.add(className);
            strengthText.textContent = text;
        });
    </script>
</body>
</html>