@extends('layout.app')

@section('title', 'تعديل التزام')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title">تعديل التزام</h1>
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

    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('obligations.update', $obligation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <ul class="list-group">
                            <li class="list-group-item">
                                <label for="expense_type" class="form-label">اختر البند</label>
                                <select class="form-control" id="expense_type" name="expense_type" required>
                                    <option value="salary" {{ $obligation->expense_type == 'salary' ? 'selected' : '' }}>الرواتب</option>
                                    <option value="rent" {{ $obligation->expense_type == 'rent' ? 'selected' : '' }}>الإيجار</option>
                                    <option value="exhibition_cost" {{ $obligation->expense_type == 'exhibition_cost' ? 'selected' : '' }}>مصروفات المعرض</option>
                                    <option value="electricity" {{ $obligation->expense_type == 'electricity' ? 'selected' : '' }}>الكهرباء</option>
                                    <option value="internet" {{ $obligation->expense_type == 'internet' ? 'selected' : '' }}>الإنترنت</option>
                                </select>
                            </li>

                            <li class="list-group-item">
                                <label for="detail" class="form-label">التفاصيل</label>
                                <textarea class="form-control" id="detail" name="detail" rows="1">{{ $obligation->detail }}</textarea>
                            </li>

                            <li class="list-group-item">
                                <label for="payment_type" class="form-label">طريقة الدفع</label>
                                <select class="form-control" id="payment_type" name="payment_type" required>
                                    <option value="cash" {{ $obligation->payment_type == 'cash' ? 'selected' : '' }}>نقدًا</option>
                                    <option value="bank" {{ $obligation->payment_type == 'bank' ? 'selected' : '' }}>بنكي</option>
                                    <option value="mixed" {{ $obligation->payment_type == 'mixed' ? 'selected' : '' }}>مختلط</option>
                                </select>
                            </li>

                            <li class="list-group-item">
                                <label for="cash_amount" class="form-label">المبلغ النقدي</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="cash_amount" name="cash_amount" value="{{ $obligation->cash_amount }}">
                            </li>

                            <li class="list-group-item">
                                <label for="bank_amount" class="form-label">المبلغ البنكي</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="bank_amount" name="bank_amount" value="{{ $obligation->bank_amount }}">
                            </li>

                            <li class="list-group-item">
                                <label for="datetime" class="form-label">التاريخ والوقت</label>
                                <input type="datetime-local" class="form-control" id="datetime" name="datetime"
                                    value="{{ \Carbon\Carbon::parse($obligation->date)->format('Y-m-d\TH:i') }}" required>
                            </li>
                        </ul>

                        <button type="submit" class="btn btn-primary mt-3">حفظ التعديلات</button>
                        <a href="{{ route('obligations.index') }}" class="btn btn-secondary mt-3">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
