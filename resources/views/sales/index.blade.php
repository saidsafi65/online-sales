@extends('layout.app')

@section('title', 'إدارة المبيعات')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .stats-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stats-label {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .action-buttons .btn {
        margin: 0 0.25rem;
    }
</style>
@endpush

@section('content')
@php
    $pageTitle = 'إدارة المبيعات';
    $pageIcon = 'fas fa-shopping-cart';
    $pageDescription = 'عرض وإدارة جميع المبيعات في النظام';
    $pageActions = '<a href="' . route('sales.create') . '" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة مبيعة جديدة
                    </a>';

    $breadcrumbs = [
        ['title' => 'الرئيسية', 'url' => route('dashboard')],
        ['title' => 'المبيعات', 'url' => '']
    ];
@endphp

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card fade-in-up">
            <div class="stats-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stats-number">{{ $statistics['totalSales'] ?? 0 }}</div>
            <div class="stats-label">إجمالي المبيعات</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card fade-in-up" style="animation-delay: 0.1s;">
            <div class="stats-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stats-number">{{ number_format($statistics['totalRevenue'] ?? 0, 2) }}</div>
            <div class="stats-label">إجمالي الإيرادات</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card fade-in-up" style="animation-delay: 0.2s;">
            <div class="stats-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-number">{{ $statistics['todaySales'] ?? 0 }}</div>
            <div class="stats-label">مبيعات اليوم</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card fade-in-up" style="animation-delay: 0.3s;">
            <div class="stats-icon">
                <i class="fas fa-undo"></i>
            </div>
            <div class="stats-number">{{ $statistics['returnedSales'] ?? 0 }}</div>
            <div class="stats-label">المبيعات المرتجعة</div>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="card fade-in-up" style="animation-delay: 0.4s;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            قائمة المبيعات
        </h5>
        <div class="card-tools">
            <button class="btn btn-outline-light btn-sm" style="color: unset;" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i>
                تصفية
            </button>
        </div>
        <div class="card-tools">
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة مبيعة جديدة
                                </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="salesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>النوع</th>
                        <th>تاريخ البيع</th>
                        <th>طريقة الدفع</th>
                        <th>المبلغ النقدي</th>
                        <th>مبلغ التطبيق</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales ?? [] as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>
                            <strong>{{ $sale->product }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $sale->type }}</span>
                        </td>
                        <td>{{ $sale->formatted_sale_date }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $sale->payment_method }}</span>
                        </td>
                        <td>{{ number_format($sale->cash_amount, 2) }} شيكل</td>
                        <td>{{ number_format($sale->app_amount, 2) }} شيكل</td>
                        <td>
                            <strong class="text-success">{{ number_format($sale->total_amount, 2) }} شيكل</strong>
                        </td>
                        <td>
                            @if($sale->is_returned)
                                <span class="badge bg-danger">مرتجع</span>
                            @else
                                <span class="badge bg-success">مكتمل</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$sale->is_returned)
                                <button class="btn btn-sm btn-outline-danger" onclick="returnSale({{ $sale->id }})" title="إرجاع">
                                    <i class="fas fa-undo"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSale({{ $sale->id }})" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد مبيعات</h5>
                                <p class="text-muted">لم يتم العثور على أي مبيعات في النظام</p>
                                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة مبيعة جديدة
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    <tr>
                        <td><a href="{{ route('sales.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة مبيعة جديدة
                                </a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- Server-side pagination links like catalog --}}
        @if(isset($sales) && $sales->hasPages())
            <div style="padding: 1.5rem 1rem; display: flex; justify-content: center; border-top: 1px solid #e2e8f0;">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-filter me-2"></i>
                    تصفية المبيعات
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('sales.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">طريقة الدفع</label>
                            <select class="form-select" name="payment_method">
                                <option value="">جميع الطرق</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                <option value="app" {{ request('payment_method') == 'app' ? 'selected' : '' }}>تطبيق</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحالة</label>
                            <select class="form-select" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>مرتجع</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>تطبيق التصفية
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#salesTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
        },
        responsive: true,
        // Disable DataTables paging/searching/info so server-side pagination (Laravel) handles it
        paging: false,
        searching: false,
        info: false,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});

function returnSale(saleId) {
    Swal.fire({
        title: 'تأكيد الإرجاع',
        text: 'هل أنت متأكد من إرجاع هذه المبيعة؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، إرجاع',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // إرسال طلب الإرجاع
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
                    Swal.fire('تم الإرجاع!', 'تم إرجاع المبيعة بنجاح.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء إرجاع المبيعة.', 'error');
                }
            });
        }
    });
}

function deleteSale(saleId) {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: 'هل أنت متأكد من حذف هذه المبيعة؟ لا يمكن التراجع عن هذا الإجراء!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // إرسال طلب الحذف
            fetch(`/sales/${saleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('تم الحذف!', 'تم حذف المبيعة بنجاح.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء حذف المبيعة.', 'error');
                }
            });
        }
    });
}
</script>
@endpush
