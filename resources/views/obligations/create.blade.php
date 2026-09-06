@extends('layout.app')

@section('title', 'إضافة التزام جديد')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="welcome-title">إضافة التزام جديد</h1>
        <p class="welcome-subtitle">املأ البيانات لإضافة التزام جديد.</p>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Obligation Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('obligations.store') }}" method="POST">
                        @csrf
                        <ul class="list-group">
                            <li class="list-group-item">
                                <label for="expense_type" class="form-label">اختر البند</label>
                                <select class="form-control" id="expense_type" name="expense_type" required>
                                    <option value="">اختر البند</option>
                                    <option value="salary">الرواتب</option>
                                    <option value="rent">الإيجار</option>
                                    <option value="exhibition_cost">مصروفات المعرض</option>
                                    <option value="electricity">الكهرباء</option>
                                    <option value="internet">الإنترنت</option>
                                </select>
                            </li>

                            <!-- تفصيل الحقل الذي سيظهر بعد اختيار البند -->
                            <li class="list-group-item" id="detail_field">
                                <label for="detail" class="form-label">التفاصيل</label>
                                <textarea class="form-control" id="detail" name="detail" rows="1"></textarea>
                            </li>

                            <li class="list-group-item">
                                <label for="payment_type" class="form-label">طريقة الدفع</label>
                                <select class="form-control" id="payment_type" name="payment_type" required
                                    onchange="togglePaymentFields()">
                                    <option value="cash">نقدًا</option>
                                    <option value="bank">بنكي</option>
                                    <option value="mixed">مختلط</option>
                                </select>
                            </li>

                            <!-- Display Cash Amount Field if Payment Type is Cash or Mixed -->
                            <li class="list-group-item" id="cash_field" style="display: none;">
                                <label for="cash_amount" class="form-label">المبلغ النقدي</label>
                                <input type="number" class="form-control" id="cash_amount" name="cash_amount">
                            </li>

                            <!-- Display Bank Amount Field if Payment Type is Bank or Mixed -->
                            <li class="list-group-item" id="bank_field" style="display: none;">
                                <label for="bank_amount" class="form-label">المبلغ البنكي</label>
                                <input type="number" class="form-control" id="bank_amount" name="bank_amount">
                            </li>

                            <li class="list-group-item">
                                <label for="datetime" class="form-label">التاريخ والوقت</label>
                                <input type="datetime-local" class="form-control" id="datetime" name="datetime" required>
                            </li>
                        </ul>

                        <button type="submit" class="btn btn-primary mt-3">حفظ التزام</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Dynamic Payment Fields -->
    <script>
        function togglePaymentFields() {
            var paymentType = document.getElementById("payment_type").value;

            // Show/hide fields based on payment type
            if (paymentType === "cash") {
                document.getElementById("cash_field").style.display = "block";
                document.getElementById("bank_field").style.display = "none";
            } else if (paymentType === "bank") {
                document.getElementById("cash_field").style.display = "none";
                document.getElementById("bank_field").style.display = "block";
            } else if (paymentType === "mixed") {
                document.getElementById("cash_field").style.display = "block";
                document.getElementById("bank_field").style.display = "block";
            }
        }

        // إظهار حقل التفاصيل عندما يتم اختيار بند محدد
        function toggleDetailField() {
            var expenseType = document.getElementById('expense_type').value;
            var detailField = document.getElementById('detail_field');

            if (expenseType) {
                detailField.style.display = 'block'; // إظهار الحقل
            } else {
                detailField.style.display = 'none'; // إخفاء الحقل
            }
        }
        
        // Initialize the correct field display when the page loads
        window.onload = function() {
            togglePaymentFields(); // Call function to set the initial state
        };
        
        // الحصول على التاريخ والوقت الحالي
        let now = new Date();
        // تنسيق التاريخ والوقت بشكل مناسب لـ datetime-local (YYYY-MM-DDTHH:mm)
        let datetime = now.toISOString().slice(0, 16);
        // تعيين التاريخ والوقت في حقل الإدخال
        document.getElementById('datetime').value = datetime;
    </script>

@endsection
