@extends('layout.app')

@section('title', 'تعديل الأمانة')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">تعديل الأمانة</h3>

    <!-- عرض رسائل الخطأ -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- نموذج تعديل الأمانة -->
    <form action="{{ route('deposits.update', $deposit->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="piece" class="form-label">القطعة</label>
            <input type="text" class="form-control" id="piece" name="piece" value="{{ old('piece', $deposit->piece) }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">النوع</label>
            <input type="text" class="form-control" id="type" name="type" value="{{ old('type', $deposit->type) }}" required>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">سبب الأخذ</label>
            <input type="text" class="form-control" id="reason" name="reason" value="{{ old('reason', $deposit->reason) }}" required>
        </div>

        <div class="mb-3">
            <label for="taken_at" class="form-label">وقت الأخذ</label>
            <input type="datetime-local" class="form-control" id="taken_at" name="taken_at" value="{{ old('taken_at', $deposit->taken_at ? \Carbon\Carbon::parse($deposit->taken_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="returned_at" class="form-label">وقت الإرجاع</label>
            <input type="datetime-local" class="form-control" id="returned_at" name="returned_at" value="{{ old('returned_at', $deposit->returned_at ? \Carbon\Carbon::parse($deposit->returned_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('deposits.index') }}" class="btn btn-secondary me-3">إلغاء</a>
            <button type="submit" class="btn btn-primary">تحديث</button>
        </div>
    </form>
</div>
@endsection
