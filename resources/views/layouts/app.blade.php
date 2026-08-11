<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPADU') - SDN Aengbaja Kenek II</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2c5282;
            --primary-dark: #152a45;
            --primary-ultra: #0f1b2e;
            --accent: #25d366;
            --accent-hover: #1da851;
            --sidebar-width: 264px;
            --sidebar-collapsed: 72px;
            --body-bg: #f0f4f8;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --card-shadow-hover: 0 10px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.04);
            --transition-smooth: cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: var(--body-bg);
            min-height: 100vh;
            color: #334155;
        }

        /* ========================================
           SIDEBAR 
        ======================================== */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-ultra) 0%, var(--primary) 40%, var(--primary-dark) 100%);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            transition: all 0.35s var(--transition-smooth);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Subtle noise texture overlay */
        .sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
            z-index: 0;
        }

        .sidebar > * { position: relative; z-index: 1; }

        /* Sidebar Brand */
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .sidebar-brand .logo-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,0.1);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.08);
            position: relative;
        }
        .sidebar-brand .logo-icon::after {
            content: '';
            position: absolute; inset: -2px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(37,211,102,0.3), transparent 60%);
            z-index: -1;
            opacity: 0.5;
        }

        .sidebar-brand .brand-text h5 {
            color: #fff;
            font-weight: 800;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }

        .sidebar-brand .brand-text small {
            color: rgba(255,255,255,0.45);
            font-size: 0.68rem;
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex: 1;
        }

        .sidebar-nav .nav-label {
            color: rgba(255,255,255,0.3);
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.85rem 0.75rem 0.4rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.6);
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.25s var(--transition-smooth);
            margin-bottom: 2px;
            position: relative;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
            transform: translateX(2px);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--accent);
            border-radius: 0 4px 4px 0;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-nav .nav-link .nav-badge {
            margin-left: auto;
            background: rgba(239,68,68,0.9);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 6px;
            min-width: 20px;
            text-align: center;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        /* ========================================
           MAIN CONTENT
        ======================================== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.35s var(--transition-smooth);
            display: flex;
            flex-direction: column;
        }

        /* ========================================
           TOP NAVBAR
        ======================================== */
        .top-navbar {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226,232,240,0.8);
            padding: 0.7rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .top-navbar .page-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--primary);
            letter-spacing: -0.01em;
        }

        .top-navbar .breadcrumb-custom {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 0.1rem;
        }
        .top-navbar .breadcrumb-custom a { color: #64748b; text-decoration: none; }
        .top-navbar .breadcrumb-custom a:hover { color: var(--primary); }

        .top-navbar .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            color: #64748b;
            font-size: 1.2rem;
            padding: 0.45rem;
            border-radius: 10px;
            transition: all 0.25s var(--transition-smooth);
        }
        .notification-btn:hover {
            background: #f1f5f9;
            color: var(--primary);
            transform: scale(1.05);
        }

        .notification-badge {
            position: absolute;
            top: 4px; right: 4px;
            width: 8px; height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #fff;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
            70% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
            100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        }

        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.35rem 0.75rem 0.35rem 0.35rem;
            cursor: pointer;
            transition: all 0.25s var(--transition-smooth);
        }
        .user-dropdown-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .user-dropdown-btn::after {
            /* Override Bootstrap caret */
            border: none !important;
            content: '\F282' !important;
            font-family: 'bootstrap-icons' !important;
            font-size: 0.7rem;
            color: #94a3b8;
            margin-left: 0.25rem;
        }

        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        /* ========================================
           CONTENT AREA 
        ======================================== */
        .content-area {
            padding: 1.5rem;
            flex: 1;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========================================
           STAT CARDS 
        ======================================== */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(226,232,240,0.8);
            transition: all 0.3s var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            opacity: 0;
            transition: opacity 0.3s;
        }
        .stat-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-3px);
        }
        .stat-card:hover::before { opacity: 1; }

        .stat-card .stat-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .stat-card .stat-label {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* ========================================
           DATA CARDS 
        ======================================== */
        .data-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(226,232,240,0.8);
            overflow: hidden;
            transition: box-shadow 0.3s var(--transition-smooth);
        }
        .data-card:hover { box-shadow: var(--card-shadow-hover); }

        .data-card .card-header-custom {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }

        .data-card .card-header-custom h6 {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .data-card .card-header-custom h6 i {
            font-size: 1rem;
            opacity: 0.6;
        }

        .data-card .card-body-custom { padding: 1.25rem; }

        /* ========================================
           TABLES 
        ======================================== */
        .table-modern {
            font-size: 0.84rem;
            margin-bottom: 0;
        }

        .table-modern thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-modern tbody tr {
            transition: background 0.2s;
        }
        .table-modern tbody tr:hover { background: #f8fafc; }

        /* ========================================
           BADGES 
        ======================================== */
        .badge-status {
            padding: 0.35em 0.8em;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-priority-high { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-priority-medium { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-priority-low { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        /* ========================================
           BUTTONS 
        ======================================== */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.84rem;
            padding: 0.55rem 1.25rem;
            transition: all 0.3s var(--transition-smooth);
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--primary-light), #3b6db5);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30,58,95,0.3);
        }

        .btn-success-wa {
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            border: none;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-success-wa:hover {
            background: linear-gradient(135deg, var(--accent-hover), #178a42);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,211,102,0.3);
        }

        /* ========================================
           EMPTY STATE 
        ======================================== */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; }
        .empty-state h6 { color: #64748b; font-weight: 600; }
        .empty-state p { color: #94a3b8; font-size: 0.85rem; }

        /* ========================================
           TIMELINE 
        ======================================== */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 8px; top: 0; bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #e2e8f0, #cbd5e1);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .timeline-item:last-child { padding-bottom: 0; }

        .timeline-dot {
            position: absolute;
            left: -2rem; top: 4px;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.6rem; color: #fff;
            z-index: 1;
        }
        .timeline-dot.completed { background: var(--accent); }
        .timeline-dot.active { background: var(--primary); animation: pulse-timeline 2s infinite; }
        .timeline-dot.pending { background: #cbd5e1; }

        @keyframes pulse-timeline {
            0% { box-shadow: 0 0 0 0 rgba(30,58,95,0.35); }
            70% { box-shadow: 0 0 0 8px rgba(30,58,95,0); }
            100% { box-shadow: 0 0 0 0 rgba(30,58,95,0); }
        }

        .timeline-content h6 { font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem; }
        .timeline-content p { font-size: 0.78rem; color: #64748b; margin-bottom: 0.15rem; }

        /* ========================================
           TOAST NOTIFICATIONS 
        ======================================== */
        .toast-container {
            position: fixed;
            top: 1rem; right: 1rem;
            z-index: 1100;
        }

        .toast-container .toast {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
            backdrop-filter: blur(10px);
            animation: slideInRight 0.4s ease-out;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* ========================================
           DROPDOWN MENU - POLISHED 
        ======================================== */
        .dropdown-menu-custom {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1) !important;
            padding: 0.5rem !important;
            animation: dropIn 0.2s ease-out;
        }

        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-menu-custom .dropdown-item {
            border-radius: 8px;
            font-size: 0.84rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s;
        }
        .dropdown-menu-custom .dropdown-item:hover { background: #f1f5f9; }
        .dropdown-menu-custom .dropdown-item.text-danger:hover { background: #fef2f2; }

        /* ========================================
           SIDEBAR TOGGLE (MOBILE) 
        ======================================== */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--primary);
            padding: 0.25rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .sidebar-toggle:hover { background: #f1f5f9; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,27,46,0.6);
            backdrop-filter: blur(4px);
            z-index: 1035;
            transition: opacity 0.3s;
        }

        /* ========================================
           SCROLLBAR 
        ======================================== */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }

        /* Main scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ========================================
           RESPONSIVE 
        ======================================== */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: inline-flex; }
        }

        @media (max-width: 575.98px) {
            .content-area { padding: 1rem; }
            .stat-card .stat-value { font-size: 1.5rem; }
            .top-navbar { padding: 0.6rem 1rem; }
        }

        @yield('styles')
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="brand-text">
                <h5>SIPADU</h5>
                <small>SDN Aengbaja Kenek II</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            @yield('sidebar')
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-2">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <span class="page-title">@yield('page-title', 'Dashboard')</span>
                    @hasSection('breadcrumb')
                        <div class="breadcrumb-custom">
                            @yield('breadcrumb')
                        </div>
                    @endif
                </div>
            </div>
            <div class="user-info">
                <button class="notification-btn" title="Notifikasi" id="notifBtn">
                    <i class="bi bi-bell"></i>
                    @if(auth()->check() && auth()->user()->inAppNotifications()->where('is_read', false)->count() > 0)
                        <span class="notification-badge"></span>
                    @endif
                </button>
                <div class="dropdown">
                    <button class="user-dropdown-btn dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</div>
                        <div class="d-none d-sm-block text-start">
                            <div style="font-size:0.8rem;font-weight:600;color:#334155;line-height:1.2">{{ auth()->user()->name ?? '' }}</div>
                            <div style="font-size:0.68rem;color:#94a3b8">{{ ucfirst(auth()->user()->role ?? '') }}</div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><hr class="dropdown-divider" style="margin:0.25rem 0"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-area">
            <!-- Toast Container -->
            <div class="toast-container" id="toastContainer">
                @if(session('success'))
                    <div class="toast show align-items-center text-bg-success border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="toast show align-items-center text-bg-danger border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="toast show align-items-center text-bg-warning border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}</div>
                            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                @endif
            </div>

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Auto-hide toasts with slide-out animation
        document.querySelectorAll('.toast.show').forEach(toast => {
            setTimeout(() => {
                toast.style.transition = 'all 0.4s ease-in';
                toast.style.transform = 'translateX(110%)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        });

        // CSRF setup for AJAX
        window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    </script>
    @stack('scripts')
</body>
</html>
