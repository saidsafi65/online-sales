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
      --primary-color: #1a56db;
      --accent-color: #6366f1;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
      --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    body {
      font-family: 'Tajawal', 'Cairo', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Header */
    .header-professional {
      background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
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
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
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
        <a class="navbar-brand" href="#">
          <div class="brand-icon"><i class="fas fa-store"></i></div>
          <div class="brand-text">
            <span class="brand-title">معرض Online Sale</span>
            <span class="brand-subtitle">منتجات حصرية</span>
          </div>
        </a>
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
        <div class="footer-info d-flex align-items-center gap-2">
          <div class="footer-logo"><i class="fas fa-store"></i></div>
          <span class="footer-text">&copy; {{ date('Y') }} معرض Online Sale. جميع الحقوق محفوظة.</span>
        </div>
        <div class="footer-links">
          <a href="#" class="footer-link">اتصل بنا</a>
          <a href="#" class="footer-link">سياسة الخصوصية</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
