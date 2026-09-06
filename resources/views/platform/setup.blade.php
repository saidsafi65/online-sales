<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إعداد حساب مدير النظام</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .card-box {
            width: 100%; max-width: 460px; background: white;
            border-radius: 20px; padding: 2.5rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .title-block { text-align: center; margin-bottom: 1.5rem; }
        .title-block i { font-size: 2.5rem; color: #1e293b; margin-bottom: 0.5rem; }
        .title-block h1 { font-size: 1.3rem; font-weight: 900; color: #1e293b; }
        .title-block p { color: #64748b; font-size: 0.85rem; }
        .form-control { border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.75rem 1rem; }
        .btn-submit {
            width: 100%; padding: 0.875rem; border: none; border-radius: 10px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white; font-weight: 700; margin-top: 1rem;
        }
        .alert { border-radius: 10px; }
        .warn { background: #fff7ed; border: 2px solid #fed7aa; color: #9a3412; border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.85rem; margin-bottom: 1.25rem; }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="title-block">
            <i class="fas fa-user-shield"></i>
            <h1>إعداد أول حساب مدير نظام</h1>
            <p>هاي الخطوة بتصير مرة وحدة بس، وبعدها هالصفحة بتقفل تلقائياً</p>
        </div>

        <div class="warn">⚠️ لو عندك حساب مدير نظام مسجّل مسبقاً، هاي الصفحة ما رح تشتغل.</div>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('system-admin.setup.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">الاسم</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-submit">إنشاء الحساب والدخول</button>
        </form>
    </div>
</body>
</html>
