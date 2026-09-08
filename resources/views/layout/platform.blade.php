<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'مدير النظام')</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f1f5f9; margin: 0; }
        .platform-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white; padding: 1rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
        }
        .platform-header .brand { font-weight: 900; font-size: 1.2rem; }
        .platform-header .brand i { margin-left: 0.5rem; }
        .platform-nav { background: white; border-bottom: 1px solid #e2e8f0; padding: 0 2rem; display: flex; gap: 1.5rem; }
        .platform-nav a {
            display: inline-block; padding: 0.9rem 0.25rem; color: #475569; text-decoration: none;
            font-weight: 600; font-size: 0.95rem; border-bottom: 3px solid transparent;
        }
        .platform-nav a.active, .platform-nav a:hover { color: #1e293b; border-bottom-color: #1e293b; }
        .platform-content { padding: 2rem; }
        .logout-btn { background: none; border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 8px; padding: 0.4rem 1rem; font-size: 0.85rem; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="platform-header">
        <div class="brand"><i class="fas fa-shield-halved"></i> لوحة مدير النظام</div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <span>{{ auth('platform')->user()->name }}</span>
            <form method="POST" action="{{ route('system-admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn">تسجيل خروج</button>
            </form>
        </div>
    </div>

    <div class="platform-nav">
        <a href="{{ route('system-admin.dashboard') }}" class="{{ request()->routeIs('system-admin.dashboard') ? 'active' : '' }}">الرئيسية</a>
        <a href="{{ route('system-admin.tenants.index') }}" class="{{ request()->routeIs('system-admin.tenants.*') ? 'active' : '' }}">المعارض</a>
        <a href="{{ route('system-admin.accounts.index') }}" class="{{ request()->routeIs('system-admin.accounts.*') ? 'active' : '' }}">الحسابات</a>
        <a href="{{ route('system-admin.reports') }}" class="{{ request()->routeIs('system-admin.reports') ? 'active' : '' }}">التقارير</a>
    </div>

    <div class="platform-content">
        @if(session('success'))
            <div class="alert alert-success" style="border-radius: 12px;">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" style="border-radius: 12px;">❌ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 12px;">
                ❌
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('migrate_output'))
            <div class="alert alert-info" style="border-radius: 12px;">
                <strong>نتيجة الترحيل:</strong>
                <pre style="white-space: pre-wrap; margin: 0.5rem 0 0; font-size: 0.85rem;">{{ session('migrate_output') }}</pre>
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
