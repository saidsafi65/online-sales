@extends('layout.app')

@section('title', 'قطع الصيانة')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-tools"></i> إدارة قطع الصيانة
                    </h4>
                    <a href="{{ route('maintenance_parts.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> إضافة جهاز جديد
                    </a>
                </div>

                {{-- البحث والفلترة --}}
                <div class="card-body">
                    <form method="GET" action="{{ route('maintenance_parts.index') }}" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="البحث عن جهاز (الاسم، الماركة، الموديل...)"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="متوفر" {{ request('status') == 'متوفر' ? 'selected' : '' }}>متوفر</option>
                                <option value="غير متوفر" {{ request('status') == 'غير متوفر' ? 'selected' : '' }}>غير متوفر</option>
                                <option value="قيد الطلب" {{ request('status') == 'قيد الطلب' ? 'selected' : '' }}>قيد الطلب</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            <a href="{{ route('maintenance_parts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- رسائل النجاح --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- جدول الأجهزة --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($parts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">الجهاز</th>
                                        <th width="12%">الماركة</th>
                                        <th width="15%">الموديل</th>
                                        <th width="25%">القطع المتوفرة</th>
                                        <th width="10%">الحالة</th>
                                        <th width="18%" class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($parts as $index => $part)
                                        <tr>
                                            <td>{{ $parts->firstItem() + $index }}</td>
                                            <td>
                                                <strong class="text-primary">
                                                    <i class="fas fa-laptop"></i> {{ $part->device_name }}
                                                </strong>
                                            </td>
                                            <td>{{ $part->brand }}</td>
                                            <td><span class="badge bg-info">{{ $part->model }}</span></td>
                                            <td>
                                                @php
                                                    $availableParts = [];
                                                    if($part->screen) $availableParts[] = 'شاشة';
                                                    if($part->motherboard) $availableParts[] = 'لوحة';
                                                    if($part->screen_cover) $availableParts[] = 'شلد';
                                                    if($part->battery) $availableParts[] = 'بطارية';
                                                    if($part->keyboard) $availableParts[] = 'كيبورد';
                                                    if($part->wifi_card) $availableParts[] = 'WiFi';
                                                @endphp
                                                
                                                @if(count($availableParts) > 0)
                                                    <small class="text-muted">
                                                        {{ implode(' • ', array_slice($availableParts, 0, 3)) }}
                                                        @if(count($availableParts) > 3)
                                                            <span class="badge bg-secondary">+{{ count($availableParts) - 3 }}</span>
                                                        @endif
                                                    </small>
                                                @else
                                                    <small class="text-muted">لا توجد قطع</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($part->status == 'متوفر')
                                                    <span class="badge bg-success">متوفر</span>
                                                @elseif($part->status == 'غير متوفر')
                                                    <span class="badge bg-danger">غير متوفر</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">قيد الطلب</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{-- عرض --}}
                                                <button type="button" 
                                                        class="btn btn-info btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewModal{{ $part->id }}"
                                                        title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- تعديل --}}
                                                <a href="{{ route('maintenance_parts.edit', $part->id) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- حذف --}}
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="confirmDelete({{ $part->id }})"
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Modal لعرض التفاصيل --}}
                                        <div class="modal fade" id="viewModal{{ $part->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-laptop"></i> 
                                                            {{ $part->device_name }} - {{ $part->brand }} {{ $part->model }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="text-primary"><i class="fas fa-info-circle"></i> معلومات الجهاز</h6>
                                                                <ul class="list-unstyled">
                                                                    <li><strong>الجهاز:</strong> {{ $part->device_name }}</li>
                                                                    <li><strong>الماركة:</strong> {{ $part->brand }}</li>
                                                                    <li><strong>الموديل:</strong> {{ $part->model }}</li>
                                                                    <li><strong>الحالة:</strong> 
                                                                        @if($part->status == 'متوفر')
                                                                            <span class="badge bg-success">متوفر</span>
                                                                        @elseif($part->status == 'غير متوفر')
                                                                            <span class="badge bg-danger">غير متوفر</span>
                                                                        @else
                                                                            <span class="badge bg-warning text-dark">قيد الطلب</span>
                                                                        @endif
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="text-primary"><i class="fas fa-cogs"></i> قطع الصيانة</h6>
                                                                <ul class="list-unstyled">
                                                                    @if($part->screen)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>شاشة:</strong> {{ $part->screen }}</li>
                                                                    @endif
                                                                    @if($part->motherboard)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>لوحة أم:</strong> {{ $part->motherboard }}</li>
                                                                    @endif
                                                                    @if($part->screen_cover)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>شلد شاشة:</strong> {{ $part->screen_cover }}</li>
                                                                    @endif
                                                                    @if($part->battery)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>بطارية:</strong> {{ $part->battery }}</li>
                                                                    @endif
                                                                    @if($part->keyboard)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>لوحة مفاتيح:</strong> {{ $part->keyboard }}</li>
                                                                    @endif
                                                                    @if($part->wifi_card)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>قطعة WiFi:</strong> {{ $part->wifi_card }}</li>
                                                                    @endif
                                                                    @if($part->hard_drive)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>هارد:</strong> {{ $part->hard_drive }}</li>
                                                                    @endif
                                                                    @if($part->ram)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>رام:</strong> {{ $part->ram }}</li>
                                                                    @endif
                                                                    @if($part->charger)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>شاحن:</strong> {{ $part->charger }}</li>
                                                                    @endif
                                                                    @if($part->fan)
                                                                        <li><i class="fas fa-check text-success"></i> <strong>مروحة:</strong> {{ $part->fan }}</li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        @if($part->other_parts)
                                                            <div class="alert alert-info">
                                                                <strong><i class="fas fa-plus-circle"></i> قطع أخرى:</strong><br>
                                                                {{ $part->other_parts }}
                                                            </div>
                                                        @endif
                                                        @if($part->notes)
                                                            <div class="alert alert-secondary">
                                                                <strong><i class="fas fa-sticky-note"></i> ملاحظات:</strong><br>
                                                                {{ $part->notes }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $parts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-toolbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد أجهزة مسجلة</h5>
                            <p class="text-muted">ابدأ بإضافة جهاز وقطعه للصيانة</p>
                            <a href="{{ route('maintenance_parts.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> إضافة جهاز جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- نموذج الحذف --}}
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('هل أنت متأكد من حذف هذا الجهاز وجميع قطعه؟')) {
        const form = document.getElementById('deleteForm');
        form.action = "{{ route('maintenance_parts.index') }}/" + id;
        form.submit();
    }
}
</script>
@endpush