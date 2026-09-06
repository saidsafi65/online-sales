@extends('layout.gust')

@section('title', 'استعادة كلمة المرور')

@push('styles')
<style>
    .auth-wrapper { display: flex; align-items: center; justify-content: center; padding: 3rem 0; }
    .auth-card {
        background: #fff; border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 440px;
        box-shadow: var(--shadow-lg); border-top: 4px solid var(--primary-color);
    }
    .auth-card h2 { text-align: center; color: var(--text-primary); font-weight: 900; margin-bottom: .8rem; }
    .auth-card p.desc { text-align: center; color: var(--text-secondary); font-size: .9rem; margin-bottom: 1.8rem; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display: block; margin-bottom: .4rem; color: var(--text-primary); font-weight: 700; font-size: .9rem; }
    .form-control { border-radius: 10px; padding: .7rem 1rem; border: 1px solid #e2e8f0; }
    .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 .2rem rgba(220,38,38,.15); }
    .btn-auth-submit {
        width: 100%; padding: .85rem; background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 1rem; margin-top: .5rem;
    }
    .btn-auth-submit:hover { opacity: .92; color: #fff; }
    .back-link { text-align: center; margin-top: 1.3rem; }
    .back-link a { color: var(--text-secondary); font-size: .85rem; text-decoration: none; }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>نسيت كلمة المرور؟</h2>
        <p class="desc">أدخل بريدك الإلكتروني وسنرسل لك رابط لإعادة تعيين كلمة المرور</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.password.email') }}">
            @csrf
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <button type="submit" class="btn-auth-submit">إرسال رابط الاستعادة</button>
        </form>

        <div class="back-link">
            <a href="{{ route('customer.login') }}">← الرجوع لتسجيل الدخول</a>
        </div>
    </div>
</div>
@endsection