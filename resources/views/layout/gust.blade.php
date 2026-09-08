<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>صفحة منتجات حصرية | Online Sale</title>

  @php $__pwaTenant = app()->bound('currentTenant') ? app('currentTenant') : null; @endphp
  <link rel="manifest" href="{{ route('manifest') }}">
  <meta name="theme-color" content="{{ $__pwaTenant->brand_primary_color ?? '#dc2626' }}">
  <link rel="apple-touch-icon" href="{{ $__pwaTenant && \Illuminate\Support\Facades\Storage::disk('public')->exists('icons/'.$__pwaTenant->id.'/apple-touch-icon.png') ? asset('storage/icons/'.$__pwaTenant->id.'/apple-touch-icon.png') : asset('icons/default/apple-touch-icon.png') }}">
  <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

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
      --primary-color: {{ app()->bound('currentTenant') ? (app('currentTenant')->brand_primary_color ?? '#dc2626') : '#dc2626' }};
      --accent-color: {{ app()->bound('currentTenant') ? (app('currentTenant')->brand_accent_color ?? '#991b1b') : '#991b1b' }};
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
      --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    html {
      overflow-x: hidden;
      width: 100%;
    }

    body {
      font-family: 'Tajawal', 'Cairo', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #fee2e2 100%);
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      width: 100%;
    }

    /* Header */
    .header-professional {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
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

    /* Cart & Account */
    .icon-link {
      color: rgba(255, 255, 255, .9) !important;
      font-weight: 700;
      font-size: .92rem;
      padding: .55rem 1rem !important;
      border-radius: 50px;
      display: flex;
      align-items: center;
      gap: .5rem;
      text-decoration: none;
      transition: all .25s ease;
      position: relative;
    }
    .icon-link:hover {
      background: rgba(255, 255, 255, .12);
      color: #fff !important;
    }
    .cart-badge {
      background: #fff;
      color: var(--primary-color);
      font-size: .68rem;
      font-weight: 900;
      min-width: 18px;
      height: 18px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 3px;
      position: absolute;
      top: 2px;
      left: 6px;
    }
    .btn-login-nav {
      background: #fff;
      color: var(--primary-color) !important;
      font-weight: 800;
      padding: .5rem 1.3rem !important;
      border-radius: 50px;
    }
    .btn-login-nav:hover {
      background: #f1f5f9;
    }

    /* Mobile cart/account row */
    .mobile-account-row {
      display: none;
    }
    @media (max-width: 991px) {
      .mobile-account-row {
        display: flex;
        gap: .6rem;
        padding: 0 1rem .9rem;
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

    /* شريط الفلاتر السريع للجوال — ثابت مع الهيدر (مش عالق بحافة الشاشة)، وبينسحب
       بالإصبع يمين/يسار زي شريط القوائم فوقه بالظبط */
    .mobile-filter-chip-bar {
      display: none;
    }
    @media (max-width: 991px) {
      .mobile-filter-chip-bar {
        display: flex;
        gap: .55rem;
        overflow-x: auto;
        padding: 0 1rem .9rem;
        -webkit-overflow-scrolling: touch;
      }
      .mobile-filter-chip-bar::-webkit-scrollbar {
        display: none;
      }
      .filter-chip-btn {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: .4rem;
        border: none;
        background: rgba(255, 255, 255, .14);
        color: rgba(255, 255, 255, .9);
        font-weight: 700;
        font-size: .82rem;
        padding: .5rem 1.05rem;
        border-radius: 50px;
        white-space: nowrap;
        font-family: inherit;
        cursor: pointer;
        transition: all .2s;
      }
      .filter-chip-btn.active {
        background: #fff;
        color: var(--primary-color);
      }
      .filter-chip-btn.chip-trigger {
        background: rgba(0, 0, 0, .18);
        color: #fff;
        position: relative;
      }
      .filter-chip-badge {
        background: #fff;
        color: var(--primary-color);
        border-radius: 50%;
        min-width: 18px;
        height: 18px;
        font-size: .7rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 .3rem;
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

  @stack('styles')
</head>

<body>
  <!-- Header -->
  <header class="header-professional">
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid">
        @php $__tenant = app()->bound('currentTenant') ? app('currentTenant') : null; @endphp
        <a class="navbar-brand" href="{{ route('products.index') }}">
          <div class="brand-icon" style="object-fit: contain; overflow: hidden;">
            <img src="{{ $__tenant && $__tenant->logo_path ? asset('storage/'.$__tenant->logo_path) : asset('images/logo.png') }}" alt="{{ $__tenant->name ?? 'Online Sale' }}" style="width:100%; height:100%; object-fit: contain;">
          </div>
          <div class="brand-text">
            <span class="brand-title">{{ $__tenant->name ?? 'معرض Online Sale' }}</span>
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
            <li class="nav-item" id="pwaInstallBtnWrap" style="display:none;">
              <button type="button" id="pwaInstallBtn" class="nav-link gust-link" style="background:none; border:none; cursor:pointer;">
                <i class="fas fa-mobile-screen-button"></i> ثبّت التطبيق
              </button>
            </li>

            {{-- السلة وتسجيل الدخول/الحساب --}}
            @auth('customer')
              @php
                  $cartCount = auth('customer')->user()->cart?->items->sum('quantity') ?? 0;
              @endphp
              <li class="nav-item">
                <a class="nav-link icon-link" href="{{ route('cart.index') }}">
                  <i class="fas fa-shopping-cart"></i>
                  <span class="cart-badge" id="cartBadgeDesktop" style="{{ $cartCount > 0 ? '' : 'display:none;' }}">{{ $cartCount }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link icon-link" href="{{ route('customer.account') }}">
                  <i class="fas fa-user-circle"></i> حسابي
                </a>
              </li>
            @else
              <li class="nav-item">
                <a class="nav-link btn-login-nav" href="{{ route('customer.login') }}">
                  <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </a>
              </li>
            @endauth
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

    @stack('mobile-filter-bar')

    <div class="mobile-account-row">
      @auth('customer')
        @php
            $cartCountMobile = auth('customer')->user()->cart?->items->sum('quantity') ?? 0;
        @endphp
        <a href="{{ route('cart.index') }}" class="mobile-quick-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">
          <i class="fas fa-shopping-cart"></i> السلة
          <span class="cart-badge" id="cartBadgeMobile" style="position:static; margin-right:.3rem; {{ $cartCountMobile > 0 ? '' : 'display:none;' }}">{{ $cartCountMobile }}</span>
        </a>
        <a href="{{ route('customer.account') }}" class="mobile-quick-link {{ request()->routeIs('customer.*') ? 'active' : '' }}">
          <i class="fas fa-user-circle"></i> حسابي
        </a>
      @else
        <a href="{{ route('customer.login') }}" class="mobile-quick-link active">
          <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
        </a>
      @endauth
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
          <div class="footer-logo" style="overflow:hidden;"><img src="{{ $__tenant && $__tenant->logo_path ? asset('storage/'.$__tenant->logo_path) : asset('images/logo.png') }}" alt="{{ $__tenant->name ?? 'Online Sale' }}" style="width:100%; height:100%; object-fit: contain;"></div>
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
    // دالة عامة لتحديث عداد السلة بالهيدر فوراً، بتُستدعى من أي صفحة بعد إضافة منتج
    function updateCartBadge(newCount) {
        const desktop = document.getElementById('cartBadgeDesktop');
        const mobile  = document.getElementById('cartBadgeMobile');

        [desktop, mobile].forEach(el => {
            if (!el) return;
            el.textContent = newCount > 99 ? '99+' : newCount;
            el.style.display = newCount > 0 ? '' : 'none';
        });
    }

    window.currentCartCount = {{ $cartCount ?? ($cartCountMobile ?? 0) }};
  </script>

  <script>
    // تسجيل Service Worker للعمل كـ PWA (يفشل بصمت على المتصفحات القديمة)
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/service-worker.js').catch(function () {});
    }
  </script>

  @if (auth()->guard('customer')->check())
  <script>
    // تفعيل إشعارات الدفع (Web Push) تلقائياً بدون زر منفصل — نفس المنطق
    // المستخدم بلوحة الموظفين، هون فقط للزبون المسجل دخوله.
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
            return fetch('{{ route('customer.push.subscribe') }}', {
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
                if (subscription) return;
                if (Notification.permission === 'denied') return;

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
  @endif

  <script>

    // زر تثبيت التطبيق
    let deferredInstallPrompt = null;
    const installBtnWrap = document.getElementById('pwaInstallBtnWrap');
    const installBtn = document.getElementById('pwaInstallBtn');

    window.addEventListener('beforeinstallprompt', function (e) {
      e.preventDefault();
      deferredInstallPrompt = e;
      if (installBtnWrap) installBtnWrap.style.display = '';
    });

    if (installBtn) {
      installBtn.addEventListener('click', function () {
        if (!deferredInstallPrompt) return;
        deferredInstallPrompt.prompt();
        deferredInstallPrompt.userChoice.finally(function () {
          deferredInstallPrompt = null;
          if (installBtnWrap) installBtnWrap.style.display = 'none';
        });
      });
    }

    window.addEventListener('appinstalled', function () {
      if (installBtnWrap) installBtnWrap.style.display = 'none';
    });
  </script>
  @stack('scripts')
</body>
</html>