@extends('layout.app')

@section('title', 'تعديل الجهاز')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> تعديل بيانات الجهاز
                    </h4>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>خطأ!</strong> يرجى تصحيح الأخطاء التالية:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>تعديل:</strong> {{ $maintenancePart->device_name }} - {{ $maintenancePart->brand }} {{ $maintenancePart->model }}
                    </div>

                    <form method="POST" action="{{ route('maintenance_parts.update', $maintenancePart->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- معلومات الجهاز الأساسية --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-laptop"></i> معلومات الجهاز</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="device_name" class="form-label">
                                            اسم الجهاز <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('device_name') is-invalid @enderror" 
                                               id="device_name" 
                                               name="device_name" 
                                               value="{{ old('device_name', $maintenancePart->device_name) }}"
                                               required>
                                        @error('device_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="brand" class="form-label">
                                            العلامة التجارية <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('brand') is-invalid @enderror" 
                                               id="brand" 
                                               name="brand" 
                                               value="{{ old('brand', $maintenancePart->brand) }}"
                                               required>
                                        @error('brand')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="model" class="form-label">
                                            الموديل <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('model') is-invalid @enderror" 
                                               id="model" 
                                               name="model" 
                                               value="{{ old('model', $maintenancePart->model) }}"
                                               required>
                                        @error('model')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- قطع الصيانة --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-cogs"></i> قطع الصيانة</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- شاشة --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="screen" class="form-label">
                                            <i class="fas fa-desktop text-primary"></i> شاشة
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="screen" 
                                               name="screen" 
                                               value="{{ old('screen', $maintenancePart->screen) }}">
                                    </div>

                                    {{-- لوحة أم --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="motherboard" class="form-label">
                                            <i class="fas fa-microchip text-success"></i> لوحة أم
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="motherboard" 
                                               name="motherboard" 
                                               value="{{ old('motherboard', $maintenancePart->motherboard) }}">
                                    </div>

                                    {{-- شلد شاشة --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="screen_cover" class="form-label">
                                            <i class="fas fa-border-all text-info"></i> شلد شاشة
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="screen_cover" 
                                               name="screen_cover" 
                                               value="{{ old('screen_cover', $maintenancePart->screen_cover) }}">
                                    </div>

                                    {{-- بطارية --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="battery" class="form-label">
                                            <i class="fas fa-battery-full text-warning"></i> بطارية
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="battery" 
                                               name="battery" 
                                               value="{{ old('battery', $maintenancePart->battery) }}">
                                    </div>

                                    {{-- لوحة مفاتيح --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="keyboard" class="form-label">
                                            <i class="fas fa-keyboard text-secondary"></i> لوحة مفاتيح
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="keyboard" 
                                               name="keyboard" 
                                               value="{{ old('keyboard', $maintenancePart->keyboard) }}">
                                    </div>

                                    {{-- قطعة WiFi --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="wifi_card" class="form-label">
                                            <i class="fas fa-wifi text-primary"></i> قطعة WiFi
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="wifi_card" 
                                               name="wifi_card" 
                                               value="{{ old('wifi_card', $maintenancePart->wifi_card) }}">
                                    </div>

                                    {{-- هارد --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="hard_drive" class="form-label">
                                            <i class="fas fa-hdd text-danger"></i> هارد
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="hard_drive" 
                                               name="hard_drive" 
                                               value="{{ old('hard_drive', $maintenancePart->hard_drive) }}">
                                    </div>

                                    {{-- رام --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ram" class="form-label">
                                            <i class="fas fa-memory text-success"></i> رام
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="ram" 
                                               name="ram" 
                                               value="{{ old('ram', $maintenancePart->ram) }}">
                                    </div>

                                    {{-- شاحن --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="charger" class="form-label">
                                            <i class="fas fa-plug text-warning"></i> شاحن
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="charger" 
                                               name="charger" 
                                               value="{{ old('charger', $maintenancePart->charger) }}">
                                    </div>

                                    {{-- مروحة --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="fan" class="form-label">
                                            <i class="fas fa-fan text-info"></i> مروحة
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="fan" 
                                               name="fan" 
                                               value="{{ old('fan', $maintenancePart->fan) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- معلومات إضافية --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> معلومات إضافية</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- قطع أخرى --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="other_parts" class="form-label">
                                            <i class="fas fa-plus-circle"></i> قطع أخرى
                                        </label>
                                        <textarea class="form-control" 
                                                  id="other_parts" 
                                                  name="other_parts" 
                                                  rows="2">{{ old('other_parts', $maintenancePart->other_parts) }}</textarea>
                                    </div>

                                    {{-- ملاحظات --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="notes" class="form-label">
                                            <i class="fas fa-sticky-note"></i> ملاحظات
                                        </label>
                                        <textarea class="form-control" 
                                                  id="notes" 
                                                  name="notes" 
                                                  rows="3">{{ old('notes', $maintenancePart->notes) }}</textarea>
                                    </div>

                                    {{-- الحالة --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">
                                            الحالة <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="متوفر" {{ old('status', $maintenancePart->status) == 'متوفر' ? 'selected' : '' }}>متوفر</option>
                                            <option value="غير متوفر" {{ old('status', $maintenancePart->status) == 'غير متوفر' ? 'selected' : '' }}>غير متوفر</option>
                                            <option value="قيد الطلب" {{ old('status', $maintenancePart->status) == 'قيد الطلب' ? 'selected' : '' }}>قيد الطلب</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- أزرار الإجراءات --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('maintenance_parts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> إلغاء والعودة
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg text-dark">
                                <i class="fas fa-save"></i> حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="far fa-clock"></i> 
                        آخر تحديث: {{ $maintenancePart->updated_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection