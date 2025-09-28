@extends('layout.app')

@section('title', 'الصفحة الرئيسية')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">مرحباً بك</h2>
        <p class="text-muted mb-0">اختر القسم الذي تود الدخول إليه</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- المبيعات -->
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('sales.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 hover-shadow">
                    <div class="card-body text-center py-5">
                        <div class="display-5 text-primary mb-3">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h5 class="card-title mb-2">المبيعات</h5>
                        <p class="card-text text-muted">إدارة عمليات البيع وعرض السجل</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- الصيانة -->
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('repairs.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 hover-shadow">
                    <div class="card-body text-center py-5">
                        <div class="display-5 text-warning mb-3">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h5 class="card-title mb-2">الصيانة</h5>
                        <p class="card-text text-muted">إدارة الصيانة والعملاء وسجل الإصلاحات</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- المشتريات -->
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('purchases.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 hover-shadow">
                    <div class="card-body text-center py-5">
                        <div class="display-5 text-success mb-3">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h5 class="card-title mb-2">المشتريات</h5>
                        <p class="card-text text-muted">تسجيل المشتريات وإدارة الموردين</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-shadow { transition: transform .2s ease, box-shadow .2s ease; }
    .hover-shadow:hover { transform: translateY(-6px); box-shadow: 0 1rem 2rem rgba(0,0,0,.08) !important; }
</style>
@endpush
