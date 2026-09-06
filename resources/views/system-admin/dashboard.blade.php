@extends('layout.platform')

@section('title', 'الرئيسية - مدير النظام')

@section('content')
<h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b; margin-bottom: 1.5rem;">لوحة التحكم</h1>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
        <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">إجمالي المعارض</div>
        <div style="font-size: 2.5rem; font-weight: 900; color: #1e293b;">{{ $totalTenants }}</div>
    </div>
    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
        <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">معارض فعّالة</div>
        <div style="font-size: 2.5rem; font-weight: 900; color: #22c55e;">{{ $activeTenants }}</div>
    </div>
    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
        <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">معارض معطّلة</div>
        <div style="font-size: 2.5rem; font-weight: 900; color: #ef4444;">{{ $inactiveTenants }}</div>
    </div>
</div>

<div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
    <a href="{{ route('system-admin.tenants.createAuto') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 600; padding: 0.75rem 1.5rem;">➕ إضافة معرض جديد</a>
    <a href="{{ route('system-admin.tenants.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px; font-weight: 600; padding: 0.75rem 1.5rem;">🏬 إدارة المعارض</a>
    <form method="POST" action="{{ route('system-admin.maintenance.migrate-all') }}">
        @csrf
        <button type="submit" class="btn btn-outline-dark" style="border-radius: 10px; font-weight: 600; padding: 0.75rem 1.5rem;" onclick="return confirm('رح يتم تشغيل ترحيل الجداول (migrate) على كل المعارض الفعّالة دفعة وحدة. متأكد؟')">
            🛠️ صيانة جميع المعارض دفعة وحدة
        </button>
    </form>
</div>
@endsection
