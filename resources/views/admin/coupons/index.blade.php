@extends('layout.app')

@section('title', 'أكواد الخصم')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title"><i class="fas fa-tags"></i> أكواد الخصم</h1>
        <p class="welcome-subtitle">إدارة أكواد الخصم اللي بيقدر العميل يستخدمها بالمتجر الإلكتروني</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> كود خصم جديد
        </a>
    </div>

    @if ($coupons->isEmpty())
        <div class="alert alert-info">ما في أكواد خصم مضافة بعد</div>
    @else
        <div class="table-responsive" style="background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
            <table class="table mb-0" style="text-align: right;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 1rem;">الكود</th>
                        <th>النوع</th>
                        <th>القيمة</th>
                        <th>الحد الأدنى للطلب</th>
                        <th>الاستخدام</th>
                        <th>الانتهاء</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coupons as $coupon)
                        <tr>
                            <td style="padding: 0.9rem 1rem; font-weight: 800; font-family: monospace;">{{ $coupon->code }}</td>
                            <td>{{ $coupon->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</td>
                            <td>{{ $coupon->type === 'percentage' ? number_format((float) $coupon->value, 0) . '%' : number_format((float) $coupon->value, 2) . ' شيكل' }}</td>
                            <td>{{ $coupon->min_order_amount ? number_format((float) $coupon->min_order_amount, 2) . ' شيكل' : '—' }}</td>
                            <td>{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}</td>
                            <td>{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '—' }}</td>
                            <td>
                                <form action="{{ route('coupons.toggle', $coupon) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="badge border-0" style="cursor: pointer; background: {{ $coupon->is_active ? '#d1fae5' : '#f1f5f9' }}; color: {{ $coupon->is_active ? '#047857' : '#64748b' }}; font-weight: 700; padding: .4rem .8rem;">
                                        {{ $coupon->is_active ? 'مفعّل' : 'معطّل' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div style="display:flex; gap:.5rem;">
                                    <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('حذف كود الخصم؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
