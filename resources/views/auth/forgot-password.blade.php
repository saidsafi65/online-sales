<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>استعادة كلمة المرور - معرض Online Sale</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #6366f1;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .forgot-container {
            width: 100%;
            max-width: 500px;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .forgot-card {
            background: white;
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }

        .page-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .form-label i {
            color: var(--secondary-color);
        }

        .form-control {
            padding: 0.875rem 1rem;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.15);
        }

        .btn-reset {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            margin-top: 1rem;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-back {
            width: 100%;
            padding: 0.875rem;
            background: transparent;
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-back:hover {
            background: var(--border-color);
            color: var(--text-primary);
        }

        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e3a8a;
            border: 2px solid #60a5fa;
        }

        .info-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 1.5rem;
            border: 2px solid #bae6fd;
        }

        .info-box-title {
            color: #0c4a6e;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box-text {
            color: #075985;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        .footer-text {
            text-align: center;
            color: white;
            margin-top: 2rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 576px) {
            .forgot-card {
                padding: 2rem 1.5rem;
            }

            .logo-icon {
                width: 70px;
                height: 70px;
                font-size: 2rem;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h1 class="page-title">استعادة كلمة المرور</h1>
                <p class="page-description">
                    أدخل بريدك الإلكتروني وسنرسل لك رابط لإعادة تعيين كلمة المرور
                </p>
            </div>

            <!-- Success Message -->
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        البريد الإلكتروني
                    </label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           placeholder="example@email.com"
                           required 
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-reset">
                    <i class="fas fa-paper-plane me-2"></i>
                    إرسال رابط إعادة التعيين
                </button>

                <!-- Back to Login -->
                <a href="{{ route('login') }}" class="btn-back">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة لتسجيل الدخول
                </a>
            </form>

            <!-- Info Box -->
            <div class="info-box">
                <div class="info-box-title">
                    <i class="fas fa-info-circle"></i>
                    تعليمات
                </div>
                <p class="info-box-text">
                    سنرسل لك رسالة بريد إلكتروني تحتوي على رابط لإعادة تعيين كلمة المرور. 
                    إذا لم تستلم الرسالة خلال بضع دقائق، تحقق من مجلد البريد المزعج.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-text">
            &copy; {{ date('Y') }} معرض Online Sale. جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>