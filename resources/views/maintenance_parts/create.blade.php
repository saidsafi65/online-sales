@extends('layout.app')

@section('title', 'إضافة جهاز جديد')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle"></i> إضافة جهاز وقطعه
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

                    <form method="POST" action="{{ route('maintenance_parts.store') }}">
                        @csrf

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
                                               value="{{ old('device_name') }}"
                                               placeholder="مثال: لابتوب"
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
                                               value="{{ old('brand') }}"
                                               placeholder="مثال: HP"
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
                                               value="{{ old('model') }}"
                                               placeholder="مثال: 250 G6"
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
                                               class="form-control @error('screen') is-invalid @enderror" 
                                               id="screen" 
                                               name="screen" 
                                               value="{{ old('screen') }}"
                                               placeholder="مثال: 15.6 inch HD LED">
                                        @error('screen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- لوحة أم --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="motherboard" class="form-label">
                                            <i class="fas fa-microchip text-success"></i> لوحة أم
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('motherboard') is-invalid @enderror" 
                                               id="motherboard" 
                                               name="motherboard" 
                                               value="{{ old('motherboard') }}"
                                               placeholder="مثال: Intel Core i5">
                                        @error('motherboard')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- شلد شاشة --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="screen_cover" class="form-label">
                                            <i class="fas fa-border-all text-info"></i> شلد شاشة
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('screen_cover') is-invalid @enderror" 
                                               id="screen_cover" 
                                               name="screen_cover" 
                                               value="{{ old('screen_cover') }}"
                                               placeholder="مثال: غطاء شاشة أسود">
                                        @error('screen_cover')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- بطارية --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="battery" class="form-label">
                                            <i class="fas fa-battery-full text-warning"></i> بطارية
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('battery') is-invalid @enderror" 
                                               id="battery" 
                                               name="battery" 
                                               value="{{ old('battery') }}"
                                               placeholder="مثال: Li-ion 41Wh">
                                        @error('battery')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- لوحة مفاتيح --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="keyboard" class="form-label">
                                            <i class="fas fa-keyboard text-secondary"></i> لوحة مفاتيح
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('keyboard') is-invalid @enderror" 
                                               id="keyboard" 
                                               name="keyboard" 
                                               value="{{ old('keyboard') }}"
                                               placeholder="مثال: عربي/إنجليزي">
                                        @error('keyboard')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- قطعة WiFi --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="wifi_card" class="form-label">
                                            <i class="fas fa-wifi text-primary"></i> قطعة WiFi
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('wifi_card') is-invalid @enderror" 
                                               id="wifi_card" 
                                               name="wifi_card" 
                                               value="{{ old('wifi_card') }}"
                                               placeholder="مثال: Intel Wireless AC 3168">
                                        @error('wifi_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- هارد --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="hard_drive" class="form-label">
                                            <i class="fas fa-hdd text-danger"></i> هارد
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('hard_drive') is-invalid @enderror" 
                                               id="hard_drive" 
                                               name="hard_drive" 
                                               value="{{ old('hard_drive') }}"
                                               placeholder="مثال: 500GB HDD">
                                        @error('hard_drive')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- رام --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ram" class="form-label">
                                            <i class="fas fa-memory text-success"></i> رام
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('ram') is-invalid @enderror" 
                                               id="ram" 
                                               name="ram" 
                                               value="{{ old('ram') }}"
                                               placeholder="مثال: 8GB DDR4">
                                        @error('ram')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- شاحن --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="charger" class="form-label">
                                            <i class="fas fa-plug text-warning"></i> شاحن
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('charger') is-invalid @enderror" 
                                               id="charger" 
                                               name="charger" 
                                               value="{{ old('charger') }}"
                                               placeholder="مثال: 65W Original">
                                        @error('charger')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- مروحة --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="fan" class="form-label">
                                            <i class="fas fa-fan text-info"></i> مروحة
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('fan') is-invalid @enderror" 
                                               id="fan" 
                                               name="fan" 
                                               value="{{ old('fan') }}"
                                               placeholder="مثال: Cooling Fan Original">
                                        @error('fan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                        <textarea class="form-control @error('other_parts') is-invalid @enderror" 
                                                  id="other_parts" 
                                                  name="other_parts" 
                                                  rows="2"
                                                  placeholder="أدخل أي قطع إضافية غير مذكورة أعلاه">{{ old('other_parts') }}</textarea>
                                        @error('other_parts')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ملاحظات --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="notes" class="form-label">
                                            <i class="fas fa-sticky-note"></i> ملاحظات
                                        </label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" 
                                                  name="notes" 
                                                  rows="3"
                                                  placeholder="أي ملاحظات أو معلومات إضافية عن الجهاز">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- الحالة --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">
                                            الحالة <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status" 
                                                required>
                                            <option value="متوفر" {{ old('status') == 'متوفر' ? 'selected' : '' }}>متوفر</option>
                                            <option value="غير متوفر" {{ old('status') == 'غير متوفر' ? 'selected' : '' }}>غير متوفر</option>
                                            <option value="قيد الطلب" {{ old('status') == 'قيد الطلب' ? 'selected' : '' }}>قيد الطلب</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- أزرار الإجراءات --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('maintenance_parts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> إلغاء والعودة
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> حفظ الجهاز
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- نصائح --}}
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb text-warning"></i> نصائح
                    </h5>
                    <ul class="mb-0">
                        <li>الحقول ذات العلامة (<span class="text-danger">*</span>) إلزامية</li>
                        <li>يمكنك ترك حقول القطع فارغة إذا لم تكن متوفرة</li>
                        <li>كن دقيقاً في إدخال معلومات الموديل للسهولة في البحث لاحقاً</li>
                        <li>استخدم حقل "قطع أخرى" لإضافة أي قطع غير موجودة في القائمة</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection