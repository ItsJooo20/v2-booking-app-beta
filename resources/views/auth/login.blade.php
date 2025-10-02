<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Booking Facility') }} - Masuk</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary:#1A73E8;--secondary:#4285F4;--light:#E8F0FE;--dark:#0D47A1;
            --accent:#34A853;--hover:#2B7DE9;--text:#202124;--text-light:#5F6368;
            --border:#DADCE0;--danger:#EA4335;--success:#34A853;
        }
        body {
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            font-family:'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
            color:var(--text);line-height:1.6;min-height:100vh;
        }
        .auth-container {
            min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;
        }
        .auth-card {
            background:rgba(255,255,255,0.95);backdrop-filter:blur(20px);
            border-radius:20px;box-shadow:0 20px 40px rgba(0,0,0,0.1);
            border:1px solid rgba(255,255,255,0.2);
            padding:2.5rem;width:100%;max-width:450px;transition:.3s;
        }
        .auth-card:hover {transform:translateY(-5px);box-shadow:0 25px 50px rgba(0,0,0,0.15);}
        .logo-container {
            width:80px;height:80px;border-radius:20px;
            background:linear-gradient(135deg,var(--primary),var(--secondary));
            display:flex;align-items:center;justify-content:center;margin:0 auto 2rem;
            box-shadow:0 10px 30px rgba(26,115,232,0.3);position:relative;overflow:hidden;
        }
        .logo-container::before {
            content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;
            background:linear-gradient(45deg,transparent,rgba(255,255,255,0.1),transparent);
            transform:rotate(45deg);animation:shimmer 3s infinite;
        }
        @keyframes shimmer {0%{transform:translateX(-100%) translateY(-100%) rotate(45deg);}100%{transform:translateX(100%) translateY(100%) rotate(45deg);}}
        .title {
            background:linear-gradient(135deg,var(--dark),var(--primary));
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            font-weight:700;font-size:2rem;margin-bottom:.5rem;
        }
        .subtitle {color:var(--text-light);font-size:1.05rem;margin-bottom:2rem;}
        .form-floating {position:relative;margin-bottom:1.5rem;}
        .form-control {
            background:rgba(255,255,255,0.9);border:2px solid var(--border);
            border-radius:12px;padding:1rem 1.25rem;font-size:1rem;transition:.3s;height:auto;
        }
        .form-control:focus {
            background:#fff;border-color:var(--primary);
            box-shadow:0 0 0 4px rgba(26,115,232,0.1);transform:translateY(-2px);
        }
        .form-control.is-invalid {
            border-color:var(--danger);box-shadow:0 0 0 4px rgba(234,67,53,0.1);
        }
        .form-floating>label {color:var(--text-light);font-weight:500;padding:1rem 1.25rem;}
        .btn-primary {
            background:linear-gradient(135deg,var(--primary),var(--secondary));
            border:none;border-radius:12px;padding:1rem 2rem;font-weight:600;
            font-size:1.05rem;letter-spacing:.5px;transition:.3s;position:relative;overflow:hidden;
        }
        .btn-primary::before {
            content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.2),transparent);
            transition:left .5s ease;
        }
        .btn-primary:hover::before {left:100%;}
        .btn-primary:hover {
            background:linear-gradient(135deg,var(--hover),var(--primary));
            transform:translateY(-3px);box-shadow:0 10px 25px rgba(26,115,232,0.3);
        }
        .password-toggle {
            position:absolute;right:1.25rem;top:50%;transform:translateY(-50%);
            background:none;border:none;color:var(--text-light);cursor:pointer;
            padding:.25rem;border-radius:4px;transition:color .2s;
        }
        .password-toggle:hover {color:var(--primary);}
        .invalid-feedback {
            color:var(--danger);font-size:.85rem;font-weight:500;
            margin-top:.5rem;display:flex;align-items:center;gap:.5rem;
        }
        .alert {
            border:none;border-radius:12px;padding:1rem 1.25rem;
            margin-bottom:1.5rem;font-weight:500;
        }
        .alert-success {background:rgba(52,168,83,0.1);color:var(--success);border-left:4px solid var(--success);}
        .alert-danger {background:rgba(234,67,53,0.1);color:var(--danger);border-left:4px solid var(--danger);}
        .auth-link {
            color:var(--secondary);text-decoration:none;font-weight:500;
            transition:.2s;position:relative;
        }
        .auth-link::after {
            content:'';position:absolute;bottom:-2px;left:0;width:0;height:2px;
            background:var(--primary);transition:width .3s;
        }
        .auth-link:hover {color:var(--primary);}
        .auth-link:hover::after {width:100%;}
        @media (max-width:576px){
            .auth-card{padding:2rem 1.5rem;margin:1rem;}
            .title{font-size:1.75rem;}
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center">
                <div class="logo-container">
                    <i class="bi bi-calendar-check text-white" style="font-size:2rem;"></i>
                </div>
                <h1 class="title">Selamat Datang</h1>
                <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @if ($errors->count() > 1)
                        Silakan periksa kesalahan berikut:
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $errors->first() }}
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="form-floating">
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Masukkan email"
                           required
                           autofocus
                           autocomplete="username">
                    <label for="email">Alamat Email</label>
                    @error('email')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-floating position-relative">
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           placeholder="Masukkan kata sandi"
                           required
                           autocomplete="current-password">
                    <label for="password">Kata Sandi</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Aktifkan jika mau remember / lupa password --}}
                {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                        <label class="form-check-label" for="remember_me">Ingat saya</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="auth-link small" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                    @endif
                </div> --}}

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                    </button>
                </div>

                {{-- @if (Route::has('register'))
                    <div class="text-center small">
                        <span class="text-muted">Belum punya akun?</span>
                        <a href="{{ route('register') }}" class="auth-link ms-1">Daftar sekarang</a>
                    </div>
                @endif --}}
            </form>
        </div>
    </div>

    <script>
        function togglePassword(){
            const field = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if(field.type === 'password'){
                field.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(a => {
                a.style.transition = 'opacity .5s';
                a.style.opacity = '0';
                setTimeout(()=>a.remove(),500);
            });
        }, 5000);
    </script>
</body>
</html>