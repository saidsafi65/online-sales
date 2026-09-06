@extends('layout.app')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">قائمة المخزن الخارجي للمعرض</h3>
                        <a href="{{ route('store.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> إضافة منتج جديد
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- رسائل النجاح --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    {{-- إحصائيات --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>إجمالي النقدي</h6>
                                    <h3>{{ number_format($totalCash, 2) }} شيكل</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>إجمالي البنكي</h6>
                                    <h3>{{ number_format($totalBank, 2) }} شيكل</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>الإجمالي الكلي</h6>
                                    <h3>{{ number_format($totalCash + $totalBank, 2) }} شيكل</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- بحث --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('store.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="البحث عن منتج أو مورد..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                    @if(request('search'))
                                    <a href="{{ route('store.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- الجدول --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>اسم المنتج</th>
                                    <th>النوع</th>
                                    <th>الكمية</th>
                                    <th>المورد</th>
                                    <th>سعر الجملة</th>
                                    <th>طريقة الدفع</th>
                                    <th>نقدي</th>
                                    <th>بنكي</th>
                                    <th>تاريخ الإضافة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->product_type }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->supplier_name }}</td>
                                    <td>{{ number_format($item->wholesale_price, 2) }} شيكل</td>
                                    <td>
                                        @if($item->payment_method == 'نقدي')
                                        <span class="badge bg-success">نقدي</span>
                                        @elseif($item->payment_method == 'بنكي')
                                        <span class="badge bg-info">بنكي</span>
                                        @else
                                        <span class="badge bg-warning">مختلط</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->cash_amount, 2) }} شيكل</td>
                                    <td>{{ number_format($item->bank_amount, 2) }} شيكل</td>
                                    <td>{{ $item->date_added }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('store.edit', $item->id) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('store.destroy', $item->id) }}" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        لا توجد منتجات في المخزن
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- الترقيم --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}
.card-header {
    border-radius: 15px 15px 0 0 !important;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.table th {
    font-weight: 600;
}
</style>
@endsection