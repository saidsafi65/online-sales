<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'نظام إدارة المبيعات')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Cairo:wght@300;400;600;700;900&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1a56db;
            --secondary-color: #0e7490;
            --accent-color: #6366f1;
            --dark-bg: #0f172a;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #0ea5e9;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .header-professional {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid var(--accent-color);
        }

        .navbar {
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 900;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: transform 0.3s ease;
            text-decoration: none;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
            box-shadow: var(--shadow-md);
            flex-shrink: 0;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            font-weight: 400;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        .navbar-toggler {
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.6rem 1.2rem !important;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
            box-shadow: var(--shadow-sm);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            left: 0;
            height: 3px;
            background: white;
            border-radius: 3px 3px 0 0;
        }

        /* User Menu */
        .user-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-section:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
        }

        .user-role {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-xl);
            border-radius: 12px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            min-width: 220px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.7rem 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-item:hover {
            background: var(--light-bg);
            transform: translateX(-3px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 3rem 0;
        }

        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Welcome Section */
        .welcome-section {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInDown 0.6s ease-out;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Service Cards */
        .service-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow-md);
            border: 2px solid transparent;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-xl);
            border-color: var(--card-color);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .service-card:hover .service-icon {
            transform: rotateY(360deg) scale(1.1);
        }

        .service-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }

        .service-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* Card Colors */
        .card-primary {
            --card-color: #1e40af;
            --card-color-light: #6366f1;
        }

        .card-primary .service-icon {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: var(--card-color);
        }

        .card-warning {
            --card-color: #f59e0b;
            --card-color-light: #fbbf24;
        }

        .card-warning .service-icon {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: var(--card-color);
        }

        .card-success {
            --card-color: #10b981;
            --card-color-light: #34d399;
        }

        .card-success .service-icon {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: var(--card-color);
        }

        .card-info {
            --card-color: #0ea5e9;
            --card-color-light: #38bdf8;
        }

        .card-info .service-icon {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: var(--card-color);
        }

        .card-purple {
            --card-color: #8b5cf6;
            --card-color-light: #a78bfa;
        }

        .card-purple .service-icon {
            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
            color: var(--card-color);
        }

        /* Footer */
        .footer-professional {
            background: white;
            border-top: 1px solid var(--border-color);
            padding: 2rem 0;
            margin-top: auto;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .footer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .footer-logo {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .footer-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            font-size: 0.9rem;
        }

        .footer-link:hover {
            color: var(--primary-color);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .service-card {
            animation: fadeIn 0.6s ease-out backwards;
        }

        .service-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .service-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .service-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .service-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .service-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .service-card:nth-child(6) {
            animation-delay: 0.6s;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(30, 64, 175, 0.98);
                padding: 1rem;
                border-radius: 12px;
                margin-top: 1rem;
            }

            .nav-link {
                margin: 0.25rem 0;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-info {
                justify-content: center;
            }

            .footer-links {
                flex-direction: column;
                gap: 0.5rem;
            }

            .brand-text {
                display: none;
            }

            .user-info {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 2rem 0;
            }

            .welcome-section {
                margin-bottom: 2rem;
            }

            .service-card {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .welcome-title {
                font-size: 1.75rem;
            }

            .welcome-subtitle {
                font-size: 1rem;
            }

            .service-icon {
                width: 70px;
                height: 70px;
                font-size: 2rem;
            }

            .service-title {
                font-size: 1.2rem;
            }

            .service-description {
                font-size: 0.9rem;
            }

            .navbar {
                padding: 0.75rem 0;
            }

            .brand-icon {
                width: 40px;
                height: 40px;
                font-size: 1.3rem;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Pagination arrows style fix: normalize SVG size and alignment */
        .pagination,
        .pagination .page-item,
        .pagination .page-link {
            line-height: 1;
        }

        /* Target SVGs used as pagination arrows (more specific selectors to override other styles) */
        .pagination .page-link svg,
        .pagination li a svg,
        .pagination svg {
            width: 18px !important;
            /* smaller, consistent arrow size */
            height: 18px !important;
            display: inline-block !important;
            vertical-align: middle !important;
            max-width: 22px !important;
        }

        .pagination .page-link {
            font-size: 0.9rem !important;
            padding: 6px 12px !important;
            border-radius: 8px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 1rem;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination .page-item .page-link:hover {
            background-color: var(--accent-color);
            color: white;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Header -->
    <header class="header-professional">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <div class="brand-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-title">معرض Online Sale</span>
                        <span class="brand-subtitle">نظام إدارة متكامل</span>
                    </div>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i>
                                <span>الرئيسية</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"
                                href="{{ route('invoices.index') }}">
                                <i class="fas fa-file-invoice"></i>
                                <span>الفواتير</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}"
                                href="{{ route('catalog.index') }}">
                                <i class="fas fa-boxes"></i>
                                <span>الكتالوج</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('deposits.*') ? 'active' : '' }}"
                                href="{{ route('deposits.index') }}">
                                <i class="fas fa-tools"></i>
                                <span>الأمانات</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                                href="{{ route('reports.index') }}">
                                <i class="fas fa-chart-line"></i>
                                <span>التقارير</span>
                            </a>
                        </li>
                    </ul>
                    @php
                        $user = auth()->user();
                        $isAdmin = auth()->check() && method_exists($user, 'isAdmin') ? (bool) $user->isAdmin() : false;
                    @endphp
                    <!-- User Menu -->
                    @if (auth()->check())
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle p-0" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    <div class="user-section">
                                        <img src="https://ui-avatars.com/api/?name=Admin&background=fff&color=1e40af&bold=true"
                                            alt="User" class="user-avatar">
                                        <div class="user-info d-none d-md-flex">
                                            <span class="user-name">{{ $user->name ?? 'مستخدم' }}</span>
                                            <span class="user-role">
                                                @if (!empty($user->role ?? null))
                                                    @if ($user->role === 'employee')
                                                        موظف
                                                    @elseif ($user->role === 'manager')
                                                        مدير فرع
                                                    @elseif ($user->role === 'admin')
                                                        مسؤول النظام
                                                    @else
                                                        غير محدد
                                                    @endif
                                                @endif
                                                @if (!empty($user->branch->name ?? null))
                                                    في فرع {{ $user->branch->name }}
                                                @endif
                                                @if ($isAdmin)
                                                    حساب مسؤول النظام
                                                @endif

                                            </span>
                                        </div>
                                        <i class="fas fa-chevron-down text-white"></i>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    {{-- <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user"></i>
                                        <span>الملف الشخصي</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cog"></i>
                                        <span>الإعدادات</span>
                                    </a>
                                </li> --}}
                                    @if (auth()->user()->isAdmin())
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('users.index') }}"
                                                style="color: rgb(0 0 0 / 90%) !important;">
                                                <i class="fas fa-users" style="color: blue;"></i>
                                                إدارة المستخدمين
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('branches.index') }}"
                                                style="color: rgb(0 0 0 / 90%) !important;">
                                                <i class="fas fa-building" style="color: blue;"></i>
                                                إدارة الفروع
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt"></i>
                                                <span>تسجيل الخروج</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">تسجيل الدخول</a>
                    @endif
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-professional">
        <div class="container-fluid">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <i class="fas fa-store"></i>
                    </div>
                    <span class="footer-text">&copy; {{ date('Y') }} معرض Online Sale. جميع الحقوق محفوظة.</span>
                </div>
                <div class="footer-links">
                    <a href="#" class="footer-link">الدعم الفني</a>
                    <a href="#" class="footer-link">سياسة الخصوصية</a>
                    <a href="#" class="footer-link">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
