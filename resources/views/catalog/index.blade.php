@extends('layout.app')

@section('title', 'كتالوج المنتجات')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">كتالوج المنتجات</h3>
        <a href="{{ route('catalog.create') }}" class="btn btn-primary">إضافة عنصر</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المنتج</th>
                        <th>النوع / الموديل</th>
                        <th>الكمية</th>
                        <th>سعر الجملة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->wholesale_price }}</td>
                            <td>
                                <form action="{{ route('catalog.destroy', $item) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
