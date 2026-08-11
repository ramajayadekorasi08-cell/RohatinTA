<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIPADU | SDN Aengbaja Kenek II</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            background: #0b1120;
            overflow: hidden;
        }

        /* ===== LEFT: BRANDING PANEL ===== */
        .login-branding {
            background: linear-gradient(160deg, #0f1b2e 0%, #152642 40%, #1a3358 70%, #0f1b2e 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated floating orbs */
        .login-branding .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.25;
            animation: float 15s ease-in-out infinite;
        }
        .login-branding .orb-1 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, #25d366 0%, transparent 70%);
            top: -80px; right: -80px;
            animation-delay: 0s;
        }
        .login-branding .orb-2 {
            width: 250px; height: 250px;
            background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
            bottom: 5%; left: -60px;
            animation-delay: -5s;
        }
        .login-branding .orb-3 {
            width: 180px; height: 180px;
            background: radial-gradient(circle, #a78bfa 0%, transparent 70%);
            top: 50%; left: 55%;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 15px) scale(0.95); }
        }

        /* Grid pattern overlay */
        .login-branding::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
        }

        .branding-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 400px;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .branding-logo-wrapper {
            width: 100px; height: 100px;
            background: rgba(255,255,255,0.08);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
            transition: transform 0.4s ease;
        }
        .branding-logo-wrapper:hover { transform: scale(1.05) rotate(-3deg); }

        .branding-logo-wrapper::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(37,211,102,0.4), rgba(59,130,246,0.2), transparent);
            z-index: -1;
            opacity: 0.6;
        }

        .branding-logo-wrapper i { font-size: 2.75rem; color: #25d366; }

        .branding-content h1 {
            font-weight: 900; font-size: 2.25rem;
            margin-bottom: 0.35rem;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #ffffff 0%, #c4d4e8 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .branding-content h3 {
            font-weight: 600; font-size: 1rem;
            color: rgba(255,255,255,0.7);
            margin-bottom: 1.5rem;
        }

        .branding-content > p {
            color: rgba(255,255,255,0.45);
            font-size: 0.85rem; line-height: 1.7;
        }

        .branding-features {
            margin-top: 2.5rem;
            text-align: left;
        }

        .feature-item {
            display: flex; align-items: center; gap: 0.85rem;
            padding: 0.65rem 0;
            color: rgba(255,255,255,0.6);
            font-size: 0.84rem;
            transition: all 0.3s ease;
        }
        .feature-item:hover { color: rgba(255,255,255,0.9); transform: translateX(4px); }

        .feature-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.06);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.06);
            transition: all 0.3s ease;
        }
        .feature-item:hover .feature-icon {
            background: rgba(37,211,102,0.12);
            border-color: rgba(37,211,102,0.2);
        }
        .feature-icon i { color: #25d366; }

        /* ===== RIGHT: LOGIN FORM PANEL ===== */
        .login-form-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            background: #f8fafc;
            position: relative;
            overflow: hidden;
        }

        /* Subtle gradient blobs */
        .login-form-section::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,211,102,0.06) 0%, transparent 60%);
            top: -150px; right: -150px;
        }
        .login-form-section::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30,58,95,0.05) 0%, transparent 60%);
            bottom: -100px; left: -100px;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .login-form-wrapper h2 {
            font-weight: 800;
            color: #0f1b2e;
            font-size: 1.85rem;
            margin-bottom: 0.4rem;
            letter-spacing: -0.02em;
        }

        .login-form-wrapper .subtitle {
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 2rem;
        }

        /* Form inputs with icon */
        .input-group-custom {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-group-custom .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.9rem 1rem 0.9rem 3rem;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
            height: auto;
            color: #1e293b;
        }

        .input-group-custom .form-control::placeholder { color: #94a3b8; }

        .input-group-custom .form-control:focus {
            border-color: #1e3a5f;
            box-shadow: 0 0 0 4px rgba(30,58,95,0.08);
            background: #fff;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s;
            z-index: 5;
        }
        .input-group-custom .form-control:focus ~ .input-icon,
        .input-group-custom:focus-within .input-icon { color: #1e3a5f; }

        .input-group-custom .toggle-password {
            position: absolute;
            right: 1rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8;
            cursor: pointer; font-size: 1.1rem;
            z-index: 5;
            transition: color 0.2s;
        }
        .input-group-custom .toggle-password:hover { color: #1e3a5f; }

        /* Submit button */
        .btn-login {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 50%, #3b6db5 100%);
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: 0.9rem;
            font-weight: 700;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.02em;
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s;
        }
        .btn-login:hover::before { left: 100%; }
        .btn-login:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(30,58,95,0.4);
        }
        .btn-login:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 4px 15px rgba(30,58,95,0.3);
        }

        /* Spinner inside button */
        .btn-login .spinner-border {
            width: 1.2rem; height: 1.2rem;
            border-width: 2px;
            display: none;
        }
        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner-border { display: inline-block; }

        /* Alerts */
        .alert-login {
            border-radius: 12px;
            border: none;
            font-size: 0.84rem;
            padding: 0.8rem 1rem;
            backdrop-filter: blur(10px);
            animation: shakeIn 0.5s ease-out;
        }
        .alert-login.alert-danger {
            background: rgba(239,68,68,0.08);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,0.15);
        }
        .alert-login.alert-success {
            background: rgba(34,197,94,0.08);
            color: #16a34a;
            border: 1px solid rgba(34,197,94,0.15);
        }

        @keyframes shakeIn {
            0% { transform: translateX(-10px); opacity: 0; }
            50% { transform: translateX(5px); }
            100% { transform: translateX(0); opacity: 1; }
        }

        /* Footer */
        .login-footer {
            margin-top: 2.5rem;
            text-align: center;
            color: #94a3b8;
            font-size: 0.75rem;
        }
        .login-footer p { margin-bottom: 0.15rem; }

        /* Mobile branding (shows on small screens) */
        .mobile-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .mobile-brand .mobile-logo {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #1e3a5f, #2c5282);
            border-radius: 18px;
            display: inline-flex;
            align-items: center; justify-content: center;
            margin-bottom: 0.75rem;
            box-shadow: 0 8px 25px rgba(30,58,95,0.25);
        }
        .mobile-brand .mobile-logo i { font-size: 1.75rem; color: #fff; }
        .mobile-brand h4 {
            font-weight: 800; color: #0f1b2e;
            font-size: 1.2rem; margin-bottom: 0.2rem;
        }
        .mobile-brand small { color: #94a3b8; font-size: 0.78rem; }

        /* Responsive */
        @media (max-width: 991.98px) {
            .login-branding { display: none; }
            .login-form-section {
                min-height: 100vh;
                background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            }
            .mobile-brand { display: block; }
        }
        @media (min-width: 992px) {
            .mobile-brand { display: none; }
        }
        @media (max-width: 575.98px) {
            .login-form-section { padding: 1.5rem; }
            .login-form-wrapper h2 { font-size: 1.5rem; }
        }

        /* Particles canvas */
        #particlesCanvas {
            position: absolute; inset: 0;
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Left: Branding -->
            <div class="col-lg-5 login-branding">
                <div class="orb orb-1"></div>
                <div class="orb orb-2"></div>
                <div class="orb orb-3"></div>
                <canvas id="particlesCanvas"></canvas>

                <div class="branding-content">
                    <div class="branding-logo-wrapper">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h1>SIPADU</h1>
                    <h3>SDN Aengbaja Kenek II</h3>
                    <p>Platform digital untuk menyampaikan, memantau, dan mengelola pengaduan orang tua siswa secara transparan dan responsif.</p>

                    <div class="branding-features">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-send-check"></i></div>
                            <span>Pengaduan mudah dan terstruktur</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-geo-alt"></i></div>
                            <span>Tracking status real-time</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-whatsapp"></i></div>
                            <span>Notifikasi WhatsApp otomatis</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
                            <span>Dashboard analitik lengkap</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Login Form -->
            <div class="col-lg-7 login-form-section">
                <div class="login-form-wrapper">
                    <div class="mobile-brand">
                        <div class="mobile-logo"><i class="bi bi-mortarboard-fill"></i></div>
                        <h4>SIPADU</h4>
                        <small>SDN Aengbaja Kenek II</small>
                    </div>

                    <h2>Selamat Datang 👋</h2>
                    <p class="subtitle">Masuk ke Sistem Informasi Layanan Pengaduan</p>

                    @if(session('error'))
                        <div class="alert alert-danger alert-login">
                            <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-login">
                            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                        @csrf

                        <div class="input-group-custom">
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   name="username" value="{{ old('username') }}"
                                   placeholder="Username" required autofocus id="usernameField">
                            <i class="bi bi-person input-icon"></i>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-custom">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" id="passwordField"
                                   placeholder="Password" required>
                            <i class="bi bi-lock input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-login mt-2" id="loginBtn">
                            <span class="btn-text"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</span>
                            <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                        </button>
                    </form>

                    <div class="login-footer">
                        <p>&copy; {{ date('Y') }} SIPADU — SDN Aengbaja Kenek II</p>
                        <p>Layanan Pengaduan Orang Tua Siswa</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const field = document.getElementById('passwordField');
            const icon = document.getElementById('toggleIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Loading state on form submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        // Floating particles in the branding panel
        (function () {
            const canvas = document.getElementById('particlesCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let w, h, particles = [];

            function resize() {
                const parent = canvas.parentElement;
                w = canvas.width = parent.offsetWidth;
                h = canvas.height = parent.offsetHeight;
            }

            function createParticles() {
                particles = [];
                const count = Math.floor((w * h) / 12000);
                for (let i = 0; i < count; i++) {
                    particles.push({
                        x: Math.random() * w,
                        y: Math.random() * h,
                        r: Math.random() * 1.5 + 0.5,
                        dx: (Math.random() - 0.5) * 0.4,
                        dy: (Math.random() - 0.5) * 0.4,
                        opacity: Math.random() * 0.5 + 0.1,
                    });
                }
            }

            function draw() {
                ctx.clearRect(0, 0, w, h);
                particles.forEach(p => {
                    p.x += p.dx;
                    p.y += p.dy;
                    if (p.x < 0) p.x = w;
                    if (p.x > w) p.x = 0;
                    if (p.y < 0) p.y = h;
                    if (p.y > h) p.y = 0;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255,255,255,${p.opacity})`;
                    ctx.fill();
                });

                // Draw connecting lines
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 100) {
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = `rgba(255,255,255,${0.06 * (1 - dist / 100)})`;
                            ctx.lineWidth = 0.5;
                            ctx.stroke();
                        }
                    }
                }
                requestAnimationFrame(draw);
            }

            resize();
            createParticles();
            draw();
            window.addEventListener('resize', () => { resize(); createParticles(); });
        })();
    </script>
</body>
</html>
