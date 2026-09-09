<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'نظام إدارة المبيعات')</title>

    @php $__pwaTenant = app()->bound('currentTenant') ? app('currentTenant') : null; @endphp
    <link rel="manifest" href="{{ route('manifest') }}">
    <meta name="theme-color" content="{{ $__pwaTenant->brand_primary_color ?? '#dc2626' }}">
    <link rel="apple-touch-icon" href="{{ $__pwaTenant && \Illuminate\Support\Facades\Storage::disk('public')->exists('icons/'.$__pwaTenant->id.'/apple-touch-icon.png') ? asset('storage/icons/'.$__pwaTenant->id.'/apple-touch-icon.png') : asset('icons/default/apple-touch-icon.png') }}">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

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
            --primary-color: {{ app()->bound('currentTenant') ? (app('currentTenant')->brand_primary_color ?? '#dc2626') : '#dc2626' }};
            --secondary-color: {{ app()->bound('currentTenant') ? (app('currentTenant')->brand_accent_color ?? '#991b1b') : '#991b1b' }};
            --accent-color: {{ app()->bound('currentTenant') ? (app('currentTenant')->brand_accent_color ?? '#b91c1c') : '#b91c1c' }};
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
            background: linear-gradient(135deg, #f8fafc 0%, #fee2e2 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .header-professional {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
            overflow: hidden;
        }

        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
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

        /* الموضع الصحيح مضمون هون صراحة — القاعدة الجاهزة من Bootstrap (dropdown-menu-end/RTL)
           كانت عم تخلي القائمة تنفتح بعيدة كتير عن زر فتحها بدل ما تنلزق فيه. */
        #userAccountDropdown .dropdown-menu {
            position: absolute !important;
            inset: auto !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            margin-top: 0.5rem !important;
            transform: none !important;
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
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
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
            --card-color: #b91c1c;
            --card-color-light: #dc2626;
        }

        .card-primary .service-icon {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
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
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .footer-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 3px;
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
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
        .notif-bell-wrap { position: relative; margin-inline-end: .75rem; }
        .notif-bell {
            width: 42px; height: 42px; border-radius: 50%;
            background: rgba(255,255,255,.1); border: none; color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; cursor: pointer; position: relative; transition: all .2s;
        }
        .notif-bell:hover { background: rgba(255,255,255,.2); }
        .notif-badge {
            position: absolute; top: -2px; left: -2px;
            background: #fff; color: var(--primary-color);
            font-size: .68rem; font-weight: 900; min-width: 19px; height: 19px;
            border-radius: 50%; display: none; align-items: center; justify-content: center;
            padding: 0 4px; box-shadow: 0 2px 6px rgba(0,0,0,.25);
        }
        .notif-badge.show { display: flex; }
        .notif-dropdown {
            position: absolute; top: calc(100% + 12px); left: 0;
            width: 360px; max-height: 460px; overflow-y: auto;
            background: white; border-radius: 14px; box-shadow: var(--shadow-xl);
            display: none; z-index: 2000; color: var(--text-primary);
        }
        .notif-dropdown.open { display: block; }
        .notif-dropdown-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: .9rem 1.1rem; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; background: white;
        }
        .notif-dropdown-header h6 { margin: 0; font-weight: 800; font-size: .95rem; }
        .notif-mark-all {
            background: none; border: none; color: var(--primary-color);
            font-size: .78rem; font-weight: 700; cursor: pointer;
        }
        .notif-item {
            padding: .8rem 1.1rem; display: flex; gap: .7rem; align-items: flex-start;
            border-bottom: 1px solid #f1f5f9; text-decoration: none; color: var(--text-primary);
            cursor: pointer; transition: background .15s;
        }
        .notif-item:hover { background: var(--light-bg); }
        .notif-item.unread { background: #fff7f7; }
        .notif-item.unread:hover { background: #fef0f0; }
        .notif-dot {
            width: 8px; height: 8px; border-radius: 50%; background: var(--primary-color);
            margin-top: .4rem; flex-shrink: 0; visibility: hidden;
        }
        .notif-item.unread .notif-dot { visibility: visible; }
        .notif-icon {
            width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: .9rem;
        }
        .notif-icon.type-low_stock { background: #fef3c7; color: #b45309; }
        .notif-icon.type-out_of_stock { background: #fee2e2; color: #b91c1c; }
        .notif-icon.type-new_order { background: #d1fae5; color: #047857; }
        .notif-body { flex: 1; min-width: 0; }
        .notif-title { font-weight: 700; font-size: .87rem; margin-bottom: .15rem; }
        .notif-desc { font-size: .8rem; color: var(--text-secondary); }
        .notif-time { font-size: .72rem; color: #94a3b8; margin-top: .2rem; }
        .notif-empty { padding: 2rem 1rem; text-align: center; color: var(--text-secondary); font-size: .85rem; }
        .notif-dropdown-footer {
            padding: .7rem 1.1rem; text-align: center; border-top: 1px solid #f1f5f9;
            position: sticky; bottom: 0; background: white;
        }
        .notif-view-all {
            color: var(--primary-color); font-size: .8rem; font-weight: 700; text-decoration: none;
        }
        .notif-view-all:hover { text-decoration: underline; }

        .notif-icon.type-catalog_created { background: #d1fae5; color: #047857; }
        .notif-icon.type-catalog_updated { background: #dbeafe; color: #1d4ed8; }
        .notif-icon.type-catalog_deleted { background: #fee2e2; color: #b91c1c; }
        .notif-icon.type-product_created,
        .notif-icon.type-laptop_created { background: #d1fae5; color: #047857; }
        .notif-icon.type-product_updated,
        .notif-icon.type-laptop_updated { background: #dbeafe; color: #1d4ed8; }
        .notif-icon.type-product_deleted,
        .notif-icon.type-laptop_deleted { background: #fee2e2; color: #b91c1c; }
        
        /* Pagination */
    .pagination,
    .pagination .page-item,
    .pagination .page-link {
        line-height: 1;
    }
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin-top: 1rem;
        flex-wrap: wrap;
    }
    .pagination .page-link {
        font-size: 0.9rem;
        padding: 6px 14px;
        border-radius: 8px;
        color: var(--primary-color);
        border: 1px solid #e2e8f0;
    }
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    .pagination .page-item.disabled .page-link {
        color: #cbd5e1;
    }
    .pagination .page-item .page-link:hover {
        background-color: var(--accent-color);
        color: white;
    }
            </style>
    <style>
        .service-card {
            height: auto;
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
                @php $__tenant = app()->bound('currentTenant') ? app('currentTenant') : null; @endphp
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <div class="brand-icon" style="overflow:hidden;">
                        <img src="{{ $__tenant && $__tenant->logo_path ? asset('storage/'.$__tenant->logo_path) : asset('images/logo.png') }}" alt="{{ $__tenant->name ?? 'Online Sale' }}" style="width:100%; height:100%; object-fit:contain;">
                    </div>
                    <div class="brand-text">
                        <span class="brand-title">{{ $__tenant->name ?? 'معرض Online Sale' }}</span>
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
                        @if (auth()->check() && auth()->user()->canViewSection('invoices'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.index') }}">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>الفواتير</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->check() && auth()->user()->canViewSection('products'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('products.*') || request()->routeIs('laptops.*') || request()->routeIs('software.*') ? 'active' : '' }}"
                                    href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-box"></i>
                                    <span>المنتجات</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('products.index') }}">
                                            <i class="fas fa-box"></i> المنتجات
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('laptops.index') }}">
                                            <i class="fas fa-laptop"></i> أجهزة اللابتوب
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('software.index') }}">
                                            <i class="fas fa-compact-disc"></i> البرامج
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        @if (auth()->check() && auth()->user()->canViewSection('catalog'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}"
                                    href="{{ route('catalog.index') }}">
                                    <i class="fas fa-boxes"></i>
                                    <span>الكتالوج</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->check() && auth()->user()->canViewSection('deposits'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('deposits.*') ? 'active' : '' }}"
                                    href="{{ route('deposits.index') }}">
                                    <i class="fas fa-tools"></i>
                                    <span>الأمانات</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->check() && auth()->user()->canViewSection('reports'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                                    href="{{ route('reports.index') }}">
                                    <i class="fas fa-chart-line"></i>
                                    <span>التقارير</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                    @php
                        $user = auth()->user();
                        $isAdmin = auth()->check() && method_exists($user, 'isAdmin') ? (bool) $user->isAdmin() : false;
                    @endphp
                    <!-- User Menu -->
                    @if (auth()->check())
                    <button type="button" id="pwaInstallBtn" class="notif-bell" style="display:none;" title="ثبّت التطبيق">
                        <i class="fas fa-mobile-screen-button"></i>
                    </button>
                    <button class="notif-bell" id="gsearchTriggerBtn" type="button" title="بحث سريع (Ctrl+K)">
                        <i class="fas fa-magnifying-glass"></i>
                    </button>
                    <div class="notif-bell-wrap">
                        <button class="notif-bell" id="notifBellBtn" type="button">
                            <i class="fas fa-bell"></i>
                            <span class="notif-badge" id="notifBadge">0</span>
                        </button>

                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-dropdown-header">
                                <h6>الإشعارات</h6>
                                <button type="button" class="notif-mark-all" id="notifMarkAllBtn">تعليم الكل كمقروء</button>
                            </div>
                            <div id="notifContent">
                                <div class="notif-empty">جاري التحميل...</div>
                            </div>
                            @if ($isAdmin)
                                <div class="notif-dropdown-footer">
                                    <a href="{{ route('activity-log.index') }}" class="notif-view-all">
                                        <i class="fas fa-list-ul me-1"></i>عرض جميع السجلات
                                    </a>
                                    <span class="text-muted">·</span>
                                    <a href="{{ route('sms-logs.index') }}" class="notif-view-all">
                                        <i class="fas fa-comment-sms me-1"></i>سجل الرسائل النصية
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown" id="userAccountDropdown" style="position: relative;">
                                <a class="nav-link dropdown-toggle p-0" href="#" role="button"
                                    data-bs-toggle="dropdown" data-bs-display="static">
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
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('branding.edit') }}"
                                                style="color: rgb(0 0 0 / 90%) !important;">
                                                <i class="fas fa-palette" style="color: blue;"></i>
                                                هوية المعرض
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('coupons.index') }}"
                                                style="color: rgb(0 0 0 / 90%) !important;">
                                                <i class="fas fa-tags" style="color: blue;"></i>
                                                أكواد الخصم
                                            </a>
                                        </li>
                                    @endif
                                    @if (auth()->user()->canViewSection('community'))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('community.index') }}"
                                                style="color: rgb(0 0 0 / 90%) !important;">
                                                <i class="fas fa-comments" style="color: blue;"></i>
                                                مجتمع المعارض
                                            </a>
                                        </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('support.index') }}"
                                            style="color: rgb(0 0 0 / 90%) !important;">
                                            <i class="fas fa-headset" style="color: blue;"></i>
                                            الدعم الفني
                                        </a>
                                    </li>
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
                    <div class="footer-logo" style="overflow:hidden;">
                        <img src="{{ $__tenant && $__tenant->logo_path ? asset('storage/'.$__tenant->logo_path) : asset('images/logo.png') }}" alt="{{ $__tenant->name ?? 'Online Sale' }}" style="width:100%; height:100%; object-fit:contain;">
                    </div>
                    <span class="footer-text">&copy; {{ date('Y') }} جميع الحقوق محفوظة للمهندس سعيد محمد صافي.</span>
                </div>
                <div class="footer-links">
                    <a href="{{ route('legal.support') }}" class="footer-link">الدعم الفني</a>
                    <a href="{{ route('legal.privacy') }}" class="footer-link">سياسة الخصوصية</a>
                    <a href="{{ route('legal.terms') }}" class="footer-link">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const bellBtn = document.getElementById('notifBellBtn');
    const dropdown = document.getElementById('notifDropdown');
    const badge = document.getElementById('notifBadge');
    const content = document.getElementById('notifContent');
    const markAllBtn = document.getElementById('notifMarkAllBtn');

    const typeIcons = {
        low_stock: 'fa-box',
        out_of_stock: 'fa-box-open',
        new_order: 'fa-shopping-bag',
        catalog_created: 'fa-plus-circle',
        catalog_updated: 'fa-edit',
        catalog_deleted: 'fa-trash-alt',
        product_created: 'fa-plus-circle',
        product_updated: 'fa-edit',
        product_deleted: 'fa-trash-alt',
        laptop_created: 'fa-plus-circle',
        laptop_updated: 'fa-edit',
        laptop_deleted: 'fa-trash-alt',
    };

    function timeAgo(dateStr) {
        const diff = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T'))) / 1000);
        if (diff < 60) return 'الآن';
        if (diff < 3600) return Math.floor(diff / 60) + ' د';
        if (diff < 86400) return Math.floor(diff / 3600) + ' س';
        return Math.floor(diff / 86400) + ' يوم';
    }

    function renderNotifications(items) {
        if (!items || items.length === 0) {
            content.innerHTML = '<div class="notif-empty">ما في تنبيهات 👍</div>';
            return;
        }

        content.innerHTML = items.map(n => `
            <div class="notif-item ${n.read_at ? '' : 'unread'}" data-id="${n.id}" data-url="${n.url || '#'}">
                <span class="notif-dot"></span>
                <span class="notif-icon type-${n.type}"><i class="fas ${typeIcons[n.type] || 'fa-bell'}"></i></span>
                <div class="notif-body">
                    <div class="notif-title">${n.title}</div>
                    <div class="notif-desc">${n.body || ''}</div>
                    <div class="notif-time">${timeAgo(n.created_at)}</div>
                </div>
            </div>
        `).join('');

        content.querySelectorAll('.notif-item').forEach(el => {
            el.addEventListener('click', function () {
                const id = this.dataset.id;
                const url = this.dataset.url;
                const wasUnread = this.classList.contains('unread');

                if (wasUnread) {
                    fetch(`/notifications/${id}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }).finally(() => {
                        if (url && url !== '#') window.location.href = url;
                    });
                } else if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        });
    }

    function loadNotifications() {
        fetch('{{ route('notifications.index') }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.classList.add('show');
            } else {
                badge.classList.remove('show');
            }
            renderNotifications(data.notifications);
        })
        .catch(() => {
            content.innerHTML = '<div class="notif-empty">تعذر تحميل التنبيهات</div>';
        });
    }

    bellBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
        if (dropdown.classList.contains('open')) loadNotifications();
    });

    markAllBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        fetch('{{ route('notifications.mark-all-read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).then(loadNotifications);
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== bellBtn) {
            dropdown.classList.remove('open');
        }
    });

    loadNotifications();
    setInterval(loadNotifications, 30000);
})();
</script>

<script>
    // تسجيل Service Worker للعمل كـ PWA (يفشل بصمت على المتصفحات القديمة)
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js').catch(function () {});
    }

    // زر تثبيت التطبيق
    let deferredInstallPrompt = null;
    const pwaInstallBtn = document.getElementById('pwaInstallBtn');

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredInstallPrompt = e;
        if (pwaInstallBtn) pwaInstallBtn.style.display = 'flex';
    });

    if (pwaInstallBtn) {
        pwaInstallBtn.addEventListener('click', function () {
            if (!deferredInstallPrompt) return;
            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.finally(function () {
                deferredInstallPrompt = null;
                pwaInstallBtn.style.display = 'none';
            });
        });
    }

    window.addEventListener('appinstalled', function () {
        if (pwaInstallBtn) pwaInstallBtn.style.display = 'none';
    });
</script>

<script>
    // تفعيل إشعارات الدفع (Web Push) تلقائياً بدون زر منفصل — نفس جرس الإشعارات الموجود
    // هو ما يظهر للمستخدم، والمتصفح نفسه يعرض طلب الإذن الأصلي عند الحاجة.
    (function () {
        const vapidMeta = document.querySelector('meta[name="vapid-public-key"]');
        if (!vapidMeta || !vapidMeta.content) return;
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; i++) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        function sendSubscription(subscription) {
            const json = subscription.toJSON();
            return fetch('{{ route('push.subscribe') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ endpoint: json.endpoint, keys: json.keys }),
            });
        }

        navigator.serviceWorker.ready.then(function (registration) {
            registration.pushManager.getSubscription().then(function (subscription) {
                if (subscription) return; // already subscribed on this device
                if (Notification.permission === 'denied') return; // user already refused, don't nag

                Notification.requestPermission().then(function (permission) {
                    if (permission !== 'granted') return;
                    registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(vapidMeta.content),
                    }).then(sendSubscription).catch(function () {});
                });
            }).catch(function () {});
        }).catch(function () {});
    })();
</script>

    @include('partials.global-search')
    @include('partials.chat-widget')

    @stack('scripts')
</body>

</html>
