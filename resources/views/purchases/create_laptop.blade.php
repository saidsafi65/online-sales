@extends('layout.app')

@section('title', 'شراء لابتوب')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header"><h3 class="mb-0"><i class="fas fa-laptop"></i> شراء لابتوب</h3></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('purchases.store-laptop') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- بيانات الشراء --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0">بيانات الشراء</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">الكمية</label>
                                <input type="number" name="quantity" min="1" class="form-control" value="{{ old('quantity', 1) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">طريقة الدفع</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">اختر</option>
                                    <option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>كاش</option>
                                    <option value="app" {{ old('payment_method')=='app'?'selected':'' }}>تطبيق</option>
                                    <option value="mixed" {{ old('payment_method')=='mixed'?'selected':'' }}>مختلط</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">التاريخ</label>
                                <input type="datetime-local" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">مبلغ كاش</label>
                                <input type="number" step="0.01" name="amount_cash" class="form-control" value="{{ old('amount_cash', 0) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">مبلغ بنك</label>
                                <input type="number" step="0.01" name="amount_bank" class="form-control" value="{{ old('amount_bank', 0) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الإجمالي (المدفوع للمورد)</label>
                                <input type="text" id="total_amount" class="form-control" value="0.00" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم المورد</label>
                                <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الجوال</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">صورة الهوية (اختياري)</label>
                                <input type="file" name="id_image" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات الشراء</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- وجهة اللابتوب --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0">هل هذا اللابتوب للبيع؟</h5></div>
                    <div class="card-body">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="for_sale" id="for_sale_yes" value="1" autocomplete="off" {{ old('for_sale', '1') == '1' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success" for="for_sale_yes"><i class="fas fa-store"></i> نعم، للبيع (يضاف للكتالوج)</label>

                            <input type="radio" class="btn-check" name="for_sale" id="for_sale_no" value="0" autocomplete="off" {{ old('for_sale') == '0' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="for_sale_no"><i class="fas fa-tools"></i> لا، لقطع الصيانة</label>
                        </div>
                    </div>
                </div>

                {{-- قسم البيع --}}
                <div id="sale_section" class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="fas fa-store"></i> بيانات اللابتوب للبيع (كتالوج اللابتوبات)</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم اللابتوب</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="مثال: Dell XPS 15">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الماركة</label>
                                <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="مثال: Dell, HP, Lenovo">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الموديل</label>
                                <input type="text" name="model" class="form-control" value="{{ old('model') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المعالج</label>
                                <input type="text" name="processor" class="form-control" value="{{ old('processor') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرام (RAM)</label>
                                <input type="text" name="ram" class="form-control" value="{{ old('ram') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الهارد (التخزين)</label>
                                <input type="text" name="storage" class="form-control" value="{{ old('storage') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">كرت الشاشة</label>
                                <input type="text" name="gpu" class="form-control" value="{{ old('gpu') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">مدة البطارية</label>
                                <input type="text" name="battery_life" class="form-control" value="{{ old('battery_life') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">سعر البيع (₪)</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نسبة الخصم (%)</label>
                                <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', 0) }}" min="0" max="100">
                            </div>
                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">صور اللابتوب</label>
                                <input type="file" name="images[]" multiple accept="image/*" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- قسم قطع الصيانة --}}
                <div id="parts_section" class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="fas fa-tools"></i> بيانات الجهاز لقطع الصيانة</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">اسم الجهاز</label>
                                <input type="text" name="device_name" class="form-control" value="{{ old('device_name') }}" placeholder="مثال: لابتوب">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">العلامة التجارية</label>
                                <input type="text" name="part_brand" class="form-control" value="{{ old('part_brand') }}" placeholder="مثال: HP">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الموديل</label>
                                <input type="text" name="part_model" class="form-control" value="{{ old('part_model') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">شاشة</label>
                                <input type="text" name="screen" class="form-control" value="{{ old('screen') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">لوحة أم</label>
                                <input type="text" name="motherboard" class="form-control" value="{{ old('motherboard') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">شلد شاشة</label>
                                <input type="text" name="screen_cover" class="form-control" value="{{ old('screen_cover') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">بطارية</label>
                                <input type="text" name="battery" class="form-control" value="{{ old('battery') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">لوحة مفاتيح</label>
                                <input type="text" name="keyboard" class="form-control" value="{{ old('keyboard') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">قطعة WiFi</label>
                                <input type="text" name="wifi_card" class="form-control" value="{{ old('wifi_card') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">هارد</label>
                                <input type="text" name="hard_drive" class="form-control" value="{{ old('hard_drive') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رام</label>
                                <input type="text" name="part_ram" class="form-control" value="{{ old('part_ram') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">شاحن</label>
                                <input type="text" name="charger" class="form-control" value="{{ old('charger') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">مروحة</label>
                                <input type="text" name="fan" class="form-control" value="{{ old('fan') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">قطع أخرى</label>
                                <textarea name="other_parts" class="form-control" rows="2">{{ old('other_parts') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات القطع</label>
                                <textarea name="part_notes" class="form-control" rows="2">{{ old('part_notes') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-select">
                                    <option value="متوفر" {{ old('status', 'متوفر') == 'متوفر' ? 'selected' : '' }}>متوفر</option>
                                    <option value="غير متوفر" {{ old('status') == 'غير متوفر' ? 'selected' : '' }}>غير متوفر</option>
                                    <option value="قيد الطلب" {{ old('status') == 'قيد الطلب' ? 'selected' : '' }}>قيد الطلب</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button class="btn btn-primary" type="submit">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const forSaleYes = document.getElementById('for_sale_yes');
    const forSaleNo = document.getElementById('for_sale_no');
    const saleSection = document.getElementById('sale_section');
    const partsSection = document.getElementById('parts_section');
    const amountCash = document.querySelector('input[name="amount_cash"]');
    const amountBank = document.querySelector('input[name="amount_bank"]');
    const totalAmount = document.getElementById('total_amount');

    function updateSectionVisibility() {
        const forSale = forSaleYes.checked;
        saleSection.classList.toggle('d-none', !forSale);
        partsSection.classList.toggle('d-none', forSale);
    }

    function calculateTotal() {
        const cash = parseFloat(amountCash.value) || 0;
        const bank = parseFloat(amountBank.value) || 0;
        totalAmount.value = (cash + bank).toFixed(2);
    }

    forSaleYes.addEventListener('change', updateSectionVisibility);
    forSaleNo.addEventListener('change', updateSectionVisibility);
    amountCash.addEventListener('input', calculateTotal);
    amountBank.addEventListener('input', calculateTotal);

    updateSectionVisibility();
    calculateTotal();
})();
</script>
@endpush
