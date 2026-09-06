@extends('layout.platform')

@section('title', 'التقارير المجمّعة - مدير النظام')

@section('content')
<h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b; margin-bottom: 1.5rem;">📊 التقارير المجمّعة (كل المعارض)</h1>

<form method="GET" action="{{ route('system-admin.reports') }}" style="display:flex; gap:0.75rem; align-items:flex-end; margin-bottom: 1.5rem;">
    <div>
        <label style="font-size:0.85rem; font-weight:600; display:block; margin-bottom:0.3rem;">من تاريخ</label>
        <input type="date" name="date_from" value="{{ $dateStart->format('Y-m-d') }}" style="border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
    </div>
    <div>
        <label style="font-size:0.85rem; font-weight:600; display:block; margin-bottom:0.3rem;">إلى تاريخ</label>
        <input type="date" name="date_to" value="{{ $dateEnd->format('Y-m-d') }}" style="border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
    </div>
    <button type="submit" class="btn btn-primary" style="border-radius:8px; font-weight:600;">فلترة</button>
</form>

<div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06); overflow-x:auto;">
    <table class="table" style="min-width:800px;">
        <thead>
            <tr>
                <th>المعرض</th>
                <th>المبيعات</th>
                <th>الصيانة</th>
                <th>المشتريات</th>
                <th>الالتزامات</th>
                <th>صافي الدخل</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>
                        <div style="font-weight:700;">{{ $row['tenant']->name }}</div>
                        <div style="font-size:0.78rem; color:#94a3b8;">{{ $row['tenant']->domain }}</div>
                        @unless ($row['ok'])
                            <div style="font-size:0.75rem; color:#ef4444;">⚠️ تعذر الاتصال بقاعدة بيانات هالمعرض</div>
                        @endunless
                    </td>
                    <td>{{ number_format($row['sales'], 2) }}</td>
                    <td>{{ number_format($row['repairs'], 2) }}</td>
                    <td>{{ number_format($row['purchases'], 2) }}</td>
                    <td>{{ number_format($row['obligations'], 2) }}</td>
                    <td style="font-weight:700; color:{{ $row['net'] >= 0 ? '#22c55e' : '#ef4444' }};">{{ number_format($row['net'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="color:#94a3b8;">لا يوجد معارض فعّالة</td></tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr style="font-weight:900; border-top:2px solid #1e293b;">
                    <td>الإجمالي</td>
                    <td>{{ number_format($totals['sales'], 2) }}</td>
                    <td>{{ number_format($totals['repairs'], 2) }}</td>
                    <td>{{ number_format($totals['purchases'], 2) }}</td>
                    <td>{{ number_format($totals['obligations'], 2) }}</td>
                    <td style="color:{{ $totals['net'] >= 0 ? '#22c55e' : '#ef4444' }};">{{ number_format($totals['net'], 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
@endsection
