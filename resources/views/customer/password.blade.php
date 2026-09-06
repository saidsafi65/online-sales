@extends('layout.gust')

@section('title', 'تغيير كلمة المرور')

@push('styles')
<style>
    .account-wrapper { padding: 1rem 0 3rem; }
    .account-title { font-size: 1.9rem; font-weight: 900; margin-bottom: 2rem; }
    .account-tabs { display: flex; gap: .6rem; flex-wrap: wrap; margin-bottom: 2rem; }
    .account-tab {
        padding: .6rem 1.3rem; border-radius: 50px; background: #fff;
        color: var(--text-primary); font-weight: 700; font-size: .9rem;
        text-decoration: none; box-shadow: var(--shadow-md);
        display: flex; align-items: center; gap: .5rem;
    }
    .account-tab.active { background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); color: #fff; }
    .form-card { background: #fff; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-md); max-width: 500px; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display: block; margin-bottom: .4rem; font-weight: 700; font-size: .9rem; }
    .form-control { border-radius: 10px; padding: .7rem 1rem; border: 1px solid #e2e8f0; }
    .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 .2rem rgba(220,38,38,.15); }
    .btn-save { padding: .8rem 2rem; background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); color: #fff; border: none; border-radius: 10px; font-weight: 700; }
    .btn-save:hover { opacity: .92; color: #fff; }
</style>
@endpush

@section('content')
<div class="account-wrapper">
    <h1 class="account-title"><i class="fas fa-user-circle"></i> حسابي</h1>

    <div class="account-tabs">
        <a href="{{ route('customer.account') }}" class="account-tab"><i class="fas fa-home"></i> نظرة عامة</a>
        <a href="{{ route('customer.profile.edit') }}" class="account-tab"><i class="fas fa-user-edit"></i> بياناتي</a>
        <a href="{{ route('customer.orders') }}" class="account-tab"><i class="fas fa-box"></i> طلباتي</a>
        <a href="{{ route('customer.wishlist') }}" class="account-tab"><i class="fas fa-heart"></i> المفضلة</a>
        <a href="{{ route('customer.password.edit') }}" class="account-tab active"><i class="fas fa-lock"></i> كلمة المرور</a>
    </div>

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

    <div class="form-card">
        <form method="POST" action="{{ route('customer.password.update') }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label>كلمة المرور الحالية</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>

            <div class="form-group mb-4">
                <label>تأكيد كلمة المرور الجديدة</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="6">
            </div>

            <button type="submit" class="btn-save"><i class="fas fa-key"></i> تغيير كلمة المرور</button>
        </form>
    </div>
</div>
@endsection