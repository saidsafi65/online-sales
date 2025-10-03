@extends('layout.app')

@section('title', 'إضافة أمانة جديدة')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">إضافة أمانة جديدة</h3>

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

    <!-- نموذج إضافة الأمانة -->
    <form action="{{ route('deposits.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="piece" class="form-label">القطعة</label>
            <input type="text" class="form-control" id="piece" name="piece" value="{{ old('piece') }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">النوع</label>
            <input type="text" class="form-control" id="type" name="type" value="{{ old('type') }}" required>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">سبب الأخذ</label>
            <input type="text" class="form-control" id="reason" name="reason" value="{{ old('reason') }}" required>
        </div>

        <div class="mb-3">
            <label for="taken_at" class="form-label">وقت الأخذ</label>
            <input type="datetime-local" class="form-control" id="taken_at" name="taken_at" value="{{ old('taken_at') }}" required>
        </div>

        {{-- <div class="mb-3">
            <label for="returned_at" class="form-label">وقت الإرجاع</label>
            <input type="datetime-local" class="form-control" id="returned_at" name="returned_at" value="{{ old('returned_at') }}">
        </div> --}}

        <div class="d-flex justify-content-end">
            <a href="{{ route('deposits.index') }}" class="btn btn-secondary me-3">إلغاء</a>
            <button type="submit" class="btn btn-primary">إضافة</button>
        </div>
    </form>
</div>

<script>
    // تعيين وقت الأخذ تلقائيًا إلى الوقت الحالي
    document.getElementById('taken_at').value = new Date().toISOString().slice(0, 16);
</script>
@endsection
