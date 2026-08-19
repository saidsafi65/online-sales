<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>صفحة منتجات حصرية | Online Sale</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Bootstrap RTL -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #dc2626;
      --accent-color: #991b1b;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
      --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    body {
      font-family: 'Tajawal', 'Cairo', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #fee2e2 100%);
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Header */
    .header-professional {
      background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
      box-shadow: var(--shadow-lg);
      border-bottom: 3px solid var(--accent-color);
      position: sticky;
      top: 0;
      z-index: 1000;
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
      text-decoration: none;
      transition: transform 0.3s ease;
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
    }

    .brand-subtitle {
      font-size: 0.7rem;
      opacity: 0.9;
    }

    /* Guest Nav (Products / Laptops / Software) */
    .gust-nav {
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .gust-link {
      color: rgba(255, 255, 255, .85) !important;
      font-weight: 700;
      font-size: .92rem;
      padding: .55rem 1.15rem !important;
      border-radius: 50px;
      display: flex;
      align-items: center;
      gap: .5rem;
      transition: all .25s ease;
    }

    .gust-link:hover {
      background: rgba(255, 255, 255, .12);
      color: #fff !important;
    }

    .gust-link.active {
      background: #fff;
      color: var(--primary-color) !important;
      box-shadow: var(--shadow-md);
    }

    .navbar-toggler {
      border-color: rgba(255, 255, 255, .4);
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    @media (max-width: 991px) {
      .gust-nav {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, .15);
      }
    }

    /* Mobile Quick Nav */
    .mobile-quick-nav {
      display: none;
    }

    @media (max-width: 991px) {
      .mobile-quick-nav {
        display: flex;
        gap: .6rem;
        overflow-x: auto;
        padding: 0 1rem .9rem;
        -webkit-overflow-scrolling: touch;
      }

      .mobile-quick-nav::-webkit-scrollbar {
        display: none;
      }

      .mobile-quick-link {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: .45rem;
        color: rgba(255, 255, 255, .85);
        background: rgba(255, 255, 255, .1);
        text-decoration: none;
        font-weight: 700;
        font-size: .85rem;
        padding: .5rem 1rem;
        border-radius: 50px;
        white-space: nowrap;
        transition: all .2s;
      }

      .mobile-quick-link.active {
        background: #fff;
        color: var(--primary-color);
      }
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 3rem 0;
    }

    .container-fluid {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 1rem;
    }

    /* Footer */
    .footer-professional {
      background: white;
      border-top: 1px solid #e2e8f0;
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

    .footer-logo {
      width: 35px;
      height: 35px;
      background: white;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
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
      transition: color 0.3s;
      font-size: 0.9rem;
    }

    .footer-link:hover {
      color: var(--primary-color);
    }

    /* Responsive */
    @media (max-width: 991px) {
      .footer-content {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>

  @stack('styles')
</head>

<body>
  <!-- Header -->
  <header class="header-professional">
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('products.index') }}">
          <div class="brand-icon"><img src="{{ asset('images/logo.png') }}" alt="Online Sale"></div>
          <div class="brand-text">
            <span class="brand-title">معرض Online Sale</span>
            <span class="brand-subtitle">منتجات حصرية</span>
          </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#gustNav" aria-controls="gustNav"
                aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="gustNav">
          <ul class="navbar-nav ms-auto gust-nav">
            <li class="nav-item">
              <a class="nav-link gust-link {{ request()->routeIs('products.index') ? 'active' : '' }}"
                 href="{{ route('products.index') }}">
                <i class="fas fa-box"></i> المنتجات
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link gust-link {{ request()->routeIs('laptops.index') ? 'active' : '' }}"
                 href="{{ route('laptops.index') }}">
                <i class="fas fa-laptop"></i> أجهزة اللابتوب
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link gust-link {{ request()->routeIs('software.index') ? 'active' : '' }}"
                 href="{{ route('software.index') }}">
                <i class="fas fa-compact-disc"></i> البرامج
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Mobile Quick Nav (always visible on small screens, no hamburger needed) -->
    <div class="mobile-quick-nav">
      <a href="{{ route('products.index') }}" class="mobile-quick-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
        <i class="fas fa-box"></i> المنتجات
      </a>
      <a href="{{ route('laptops.index') }}" class="mobile-quick-link {{ request()->routeIs('laptops.index') ? 'active' : '' }}">
        <i class="fas fa-laptop"></i> أجهزة اللابتوب
      </a>
      <a href="{{ route('software.index') }}" class="mobile-quick-link {{ request()->routeIs('software.index') ? 'active' : '' }}">
        <i class="fas fa-compact-disc"></i> البرامج
      </a>
    </div>
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
        <div class="footer-info d-flex align-items-center gap-2">
          <div class="footer-logo"><img src="{{ asset('images/logo.png') }}" alt="Online Sale"></div>
          <span class="footer-text">&copy; {{ date('Y') }} معرض Online Sale. جميع الحقوق محفوظة.</span>
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
  @stack('scripts')
</body>
</html>
