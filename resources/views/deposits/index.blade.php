@extends('layout.app')

@section('title', 'أمانات الصيانة')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-4">أمانات الصيانة</h3>
        <a href="{{ route('deposits.create') }}" class="btn btn-primary">إضافة عنصر</a>
    </div>

    <!-- عرض جدول الأمانات -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>القطعة</th>
                <th>النوع</th>
                <th>سبب الأخذ</th>
                <th>وقت الأخذ</th>
                <th>وقت الإرجاع</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deposits as $deposit)
                <tr>
                    <td>{{ $deposit->piece }}</td>
                    <td>{{ $deposit->type }}</td>
                    <td>{{ $deposit->reason }}</td>
                    <td>{{ $deposit->taken_at }}</td>
                    <td>{{ $deposit->returned_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
