<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('Panel Admin', 'Booking Facility') }}</title>

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
            background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display:flex;
            flex-direction:column;
            justify-content:center;
            padding:20px;
        }
        .welcome-container {max-width:1200px;margin:0 auto;width:100%;}
        .welcome-card {
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(20px);
            border-radius:20px;
            box-shadow:0 20px 40px rgba(0,0,0,0.1);
            border:1px solid rgba(255,255,255,0.2);
            padding:3rem;
            transition:.3s;
        }
        .welcome-card:hover {transform:translateY(-5px);box-shadow:0 25px 50px rgba(0,0,0,0.15);}
        .logo-container {
            width:100px;height:100px;border-radius:20px;
            background:linear-gradient(135deg,var(--accent),var(--secondary));
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 2rem;
            box-shadow:0 10px 30px rgba(52,168,83,0.3);
            position:relative;overflow:hidden;
        }
        .logo-container::before {
            content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;
            background:linear-gradient(45deg,transparent,rgba(255,255,255,0.1),transparent);
            transform:rotate(45deg);animation:shimmer 3s infinite;
        }
        @keyframes shimmer {
            0% {transform:translateX(-100%) translateY(-100%) rotate(45deg);}
            100% {transform:translateX(100%) translateY(100%) rotate(45deg);}
        }
        .welcome-title {
            background:linear-gradient(135deg,var(--dark),var(--accent));
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            font-weight:700;font-size:2.5rem;margin-bottom:1rem;text-align:center;
        }
        .welcome-subtitle {
            color:var(--text-light);font-size:1.2rem;margin-bottom:2rem;
            text-align:center;max-width:700px;margin-left:auto;margin-right:auto;
        }
        .feature-icon {font-size:2.5rem;margin-bottom:1rem;color:var(--primary);}
        .feature-title {font-weight:600;margin-bottom:.75rem;}
        .feature-description {color:var(--text-light);font-size:.95rem;}
        .btn-primary {
            background:linear-gradient(135deg,var(--accent),var(--secondary));
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
            background:linear-gradient(135deg,var(--success),var(--accent));
            transform:translateY(-3px);box-shadow:0 10px 25px rgba(52,168,83,0.3);
        }
        .btn-outline-primary {
            border:2px solid var(--primary);color:var(--primary);font-weight:500;
            border-radius:12px;padding:.75rem 1.5rem;transition:.3s;
        }
        .btn-outline-primary:hover {
            background:var(--primary);color:#fff;transform:translateY(-2px);
            box-shadow:0 5px 15px rgba(26,115,232,.2);
        }
        .auth-link {color:var(--secondary);text-decoration:none;font-weight:500;position:relative;transition:.2s;}
        .auth-link::after {
            content:'';position:absolute;bottom:-2px;left:0;width:0;height:2px;
            background:var(--primary);transition:width .3s ease;
        }
        .auth-link:hover {color:var(--primary);}
        .auth-link:hover::after {width:100%;}
        @media (max-width: 768px) {
            .welcome-card {padding:2rem 1.5rem;}
            .welcome-title {font-size:2rem;}
            .welcome-subtitle {font-size:1.05rem;}
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-card">
            <div class="text-center mb-5">
                <div class="logo-container">
                    <i class="bi bi-calendar-check text-white" style="font-size:2.5rem;"></i>
                </div>
                <h1 class="welcome-title">Sistem Informasi Manajemen Ruang</h1>
                <p class="welcome-subtitle">
                    Kelola dan lakukan pemesanan ruang secara mudah dan cepat. Pantau ketersediaan, lakukan perubahan, dan tingkatkan efisiensi pemakaian ruang atau fasilitas lainnya.
                </p>
            </div>

            {{-- <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <i class="bi bi-calendar2-week feature-icon"></i>
                        <h3 class="feature-title">Booking Mudah</h3>
                        <p class="feature-description">
                            Pesan fasilitas hanya dalam beberapa klik dengan tampilan yang sederhana dan nyaman digunakan.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <i class="bi bi-clock-history feature-icon"></i>
                        <h3 class="feature-title">Kelola Reservasi</h3>
                        <p class="feature-description">
                            Lihat, ubah, atau batalkan pemesanan Anda kapan saja langsung dari dashboard.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <i class="bi bi-bell feature-icon"></i>
                        <h3 class="feature-title">Notifikasi</h3>
                        <p class="feature-description">
                            Dapatkan pengingat dan update tepat waktu untuk setiap reservasi mendatang.
                        </p>
                    </div>
                </div>
            </div> --}}

            <div class="text-center mt-4">
                @if (Route::has('login'))
                    <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-speedometer2 me-2"></i> Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                            </a>
                            {{-- @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-person-plus me-2"></i> Daftar
                                </a>
                            @endif --}}
                        @endauth
                    </div>
                @endif

                {{-- <p class="text-muted small mb-0">
                    Butuh bantuan? <a href="mailto:support@example.com" class="auth-link">Hubungi Support</a>
                </p> --}}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>