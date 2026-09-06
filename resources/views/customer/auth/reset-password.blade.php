@extends('layout.gust')

@section('title', 'إعادة تعيين كلمة المرور')

@push('styles')
<style>
    .auth-wrapper { display: flex; align-items: center; justify-content: center; padding: 3rem 0; }
    .auth-card {
        background: #fff; border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 440px;
        box-shadow: var(--shadow-lg); border-top: 4px solid var(--primary-color);
    }
    .auth-card h2 { text-align: center; color: var(--text-primary); font-weight: 900; margin-bottom: 2rem; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display: block; margin-bottom: .4rem; color: var(--text-primary); font-weight: 700; font-size: .9rem; }
    .form-control { border-radius: 10px; padding: .7rem 1rem; border: 1px solid #e2e8f0; }
    .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 .2rem rgba(220,38,38,.15); }
    .btn-auth-submit {
        width: 100%; padding: .85rem; background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 1rem; margin-top: .5rem;
    }
    .btn-auth-submit:hover { opacity: .92; color: #fff; }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>إعادة تعيين كلمة المرور</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" required>
            </div>

            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>

            <div class="form-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="6">
            </div>

            <button type="submit" class="btn-auth-submit">تغيير كلمة المرور</button>
        </form>
    </div>
</div>
@endsection