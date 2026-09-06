@extends('layout.gust')

@section('title', 'تسجيل الدخول')

@push('styles')
<style>
    .auth-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 0;
    }
    .auth-card {
        background: #fff;
        border-radius: 20px;
        padding: 2.5rem;
        width: 100%;
        max-width: 440px;
        box-shadow: var(--shadow-lg);
        border-top: 4px solid var(--primary-color);
    }
    .auth-card h2 {
        text-align: center;
        color: var(--text-primary);
        font-weight: 900;
        margin-bottom: 2rem;
    }
    .auth-card .form-group { margin-bottom: 1.1rem; }
    .auth-card label {
        display: block;
        margin-bottom: .4rem;
        color: var(--text-primary);
        font-weight: 700;
        font-size: .9rem;
    }
    .auth-card .form-control {
        border-radius: 10px;
        padding: .7rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .auth-card .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 .2rem rgba(220,38,38,.15);
    }
    .btn-auth-submit {
        width: 100%;
        padding: .85rem;
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        margin-top: .5rem;
    }
    .btn-auth-submit:hover { opacity: .92; color: #fff; }
    .switch-link { text-align: center; margin-top: 1.3rem; font-size: .9rem; color: var(--text-secondary); }
    .switch-link a { color: var(--primary-color); font-weight: 700; text-decoration: none; }
    .back-link { text-align: center; margin-top: .8rem; }
    .back-link a { color: var(--text-secondary); font-size: .85rem; text-decoration: none; }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>تسجيل الدخول</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.login') }}">
            @csrf

            <div class="form-group">
                <label>البريد الإلكتروني أو رقم الجوال</label>
                <input type="text" name="login" class="form-control" value="{{ old('login') }}" required autofocus>
            </div>

            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group" style="text-align:left;">
                <a href="{{ route('customer.password.request') }}" style="font-size:.85rem;color:var(--text-secondary);text-decoration:none;">نسيت كلمة المرور؟</a>
            </div>
            <div class="form-group d-flex align-items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" style="margin-top:0;">
                <label for="remember" class="mb-0 fw-normal">تذكرني</label>
            </div>

            <button type="submit" class="btn-auth-submit">دخول</button>
        </form>

        <div class="switch-link">
            ما عندك حساب؟ <a href="{{ route('customer.register') }}">إنشاء حساب جديد</a>
        </div>

        <div class="back-link">
            <a href="{{ route('products.index') }}">← الرجوع للمنتجات</a>
        </div>
    </div>
</div>
@endsection