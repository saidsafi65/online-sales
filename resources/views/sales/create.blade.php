@extends('layout.app')

@section('title', 'إضافة عملية بيع جديدة')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">إضافة عملية بيع جديدة</h3>
            </div>
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

                <form action="{{ route('sales.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product" class="form-label">اسم المنتج</label>
                            <select class="form-select @error('product') is-invalid @enderror" id="product" name="product"
                                required>
                                <option value="">اختر المنتج</option>
                                @foreach (($products ?? collect())->keys() as $product)
                                    <option value="{{ $product }}" {{ old('product') == $product ? 'selected' : '' }}>
                                        {{ $product }}</option>
                                @endforeach
                            </select>
                            @error('product')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">النوع / الموديل</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="">اختر النوع / الموديل</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية</label>
                            <input type="number" min="1" step="1" id="quantity" name="quantity"
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', 1) }}">
                            <small class="text-muted" id="available_quantity"></small>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sale_date" class="form-label">تاريخ البيع</label>
                            <input type="datetime-local" class="form-control @error('sale_date') is-invalid @enderror"
                                id="sale_date" name="sale_date" value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}"
                                required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <script>
                            window.onload = function() {
                                // الحصول على التاريخ والوقت الحالي من جهاز المستخدم
                                let now = new Date();
                                let year = now.getFullYear();
                                let month = String(now.getMonth() + 1).padStart(2, '0');
                                let day = String(now.getDate()).padStart(2, '0');
                                let hours = String(now.getHours()).padStart(2, '0');
                                let minutes = String(now.getMinutes()).padStart(2, '0');

                                // صيغة YYYY-MM-DDTHH:MM
                                let datetimeLocalValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                // تعيين القيمة لحقل datetime-local
                                document.getElementById('sale_date').value = datetimeLocalValue;
                            }
                        </script>


                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">طريقة الدفع</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method"
                                name="payment_method" required>
                                <option value="">اختر طريقة الدفع</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                {{-- <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة
                                </option> --}}
                                <option value="app" {{ old('payment_method') == 'app' ? 'selected' : '' }}>تطبيق
                                </option>
                                <option value="mixed" {{ old('payment_method') == 'mixed' ? 'selected' : '' }}>مختلط
                                </option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="cash_amount_group" class="col-md-6 mb-3">
                            <label for="cash_amount" class="form-label">المبلغ النقدي</label>
                            <input type="number" step="0.01"
                                class="form-control @error('cash_amount') is-invalid @enderror" id="cash_amount"
                                name="cash_amount" value="{{ old('cash_amount', 0) }}" required>
                            @error('cash_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="app_amount_group" class="col-md-6 mb-3">
                            <label for="app_amount" class="form-label">مبلغ التطبيق</label>
                            <input type="number" step="0.01"
                                class="form-control @error('app_amount') is-invalid @enderror" id="app_amount"
                                name="app_amount" value="{{ old('app_amount', 0) }}" required>
                            @error('app_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ربط قائمة الأنواع مع المنتج المختار
        (function() {
            // products is an object where keys are product names and values are arrays of available types (already filtered server-side)
            const products = @json($products->map(function($arr){ return $arr->toArray(); })->toArray() ?? []);
            const productSelect = document.getElementById('product');
            const typeSelect = document.getElementById('type');

            function fillTypes(selectedProduct) {
                typeSelect.innerHTML = '<option value="">اختر النوع / الموديل</option>';
                const types = products[selectedProduct] || [];
                // If no types available, leave only the placeholder option
                if (!types || types.length === 0) {
                    return;
                }

                types.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t;
                    opt.textContent = t;
                    if (t === @json(old('type'))) opt.selected = true;
                    typeSelect.appendChild(opt);
                });
            }

            productSelect.addEventListener('change', function() {
                fillTypes(this.value);
            });

            document.addEventListener('DOMContentLoaded', function() {
                const initial = productSelect.value || @json(old('product')) || '';
                if (initial) {
                    productSelect.value = initial;
                    fillTypes(initial);
                }
            });
        })();
        (function() {
            const paymentSelect = document.getElementById('payment_method');
            const cashInput = document.getElementById('cash_amount');
            const appInput = document.getElementById('app_amount');
            const cashGroup = document.getElementById('cash_amount_group');
            const appGroup = document.getElementById('app_amount_group');

            function show(el) {
                el.classList.remove('d-none');
            }

            function hide(el) {
                el.classList.add('d-none');
            }

            function updateAmountFields() {
                const method = paymentSelect.value;
                switch (method) {
                    case 'cash':
                        // إظهار النقدي وإخفاء التطبيق
                        show(cashGroup);
                        hide(appGroup);
                        cashInput.removeAttribute('readonly');
                        appInput.value = '0';
                        appInput.setAttribute('readonly', 'readonly');
                        break;
                    case 'app':
                    case 'card':
                        // إظهار التطبيق وإخفاء النقدي
                        show(appGroup);
                        hide(cashGroup);
                        appInput.removeAttribute('readonly');
                        cashInput.value = '0';
                        cashInput.setAttribute('readonly', 'readonly');
                        break;
                    case 'mixed':
                        // إظهار القيمتين
                        show(cashGroup);
                        show(appGroup);
                        cashInput.removeAttribute('readonly');
                        appInput.removeAttribute('readonly');
                        break;
                    default:
                        // الوضع الافتراضي
                        show(cashGroup);
                        show(appGroup);
                        cashInput.removeAttribute('readonly');
                        appInput.removeAttribute('readonly');
                }
            }

            paymentSelect.addEventListener('change', updateAmountFields);
            document.addEventListener('DOMContentLoaded', updateAmountFields);
            // استدعاء فوري لضبط الحالة عند الفتح
            updateAmountFields();
        })();
    </script>
@endpush
