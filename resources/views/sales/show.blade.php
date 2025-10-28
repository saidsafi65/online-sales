@extends('layout.app')

@section('title', 'تفاصيل المبيعة')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">تفاصيل المبيعة #{{ $sale->id }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>المنتج:</strong> {{ $sale->product }}</p>
                    <p><strong>النوع:</strong> {{ $sale->type }}</p>
                    <p><strong>تاريخ البيع:</strong> {{ $sale->formatted_sale_date }}</p>
                    <p><strong>طريقة الدفع:</strong> {{ $sale->payment_method }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>المبلغ النقدي:</strong> {{ number_format($sale->cash_amount, 2) }} شيكل</p>
                    <p><strong>مبلغ التطبيق:</strong> {{ number_format($sale->app_amount, 2) }} شيكل</p>
                    <p><strong>الإجمالي:</strong> {{ number_format($sale->total_amount, 2) }} شيكل</p>
                    <p><strong>الحالة:</strong>
                        @if($sale->is_returned)
                            <span class="badge bg-danger">مرتجع</span>
                        @else
                            <span class="badge bg-success">مكتمل</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($sale->notes)
                <div class="mt-4">
                    <h5>ملاحظات:</h5>
                    <p>{{ $sale->notes }}</p>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">عودة للقائمة</a>
                <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning">تعديل</a>
                @if(!$sale->is_returned)
                    <button class="btn btn-danger" onclick="returnSale({{ $sale->id }})">إرجاع</button>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$sale->is_returned)
    @push('scripts')
    <script>
        function returnSale(saleId) {
            if (confirm('هل أنت متأكد من إرجاع هذه المبيعة؟')) {
                fetch(`/sales/${saleId}/return`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('تم إرجاع المبيعة بنجاح');
                        location.reload();
                    } else {
                        alert('حدث خطأ أثناء إرجاع المبيعة');
                    }
                });
            }
        }
    </script>
    @endpush
@endif
@endsection
