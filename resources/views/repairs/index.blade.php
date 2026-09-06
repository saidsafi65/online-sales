@extends('layout.app')

@section('title', 'الصيانة')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">سجل الصيانة</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('repairs.create') }}" class="btn btn-primary">إضافة صيانة</a>
                <a href="{{ route('repairs.create', ['type' => 'software']) }}" class="btn btn-success">تركيب ويندوز أو برامج</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <x-filter-bar :action="route('repairs.index')" :show-search="true" search-placeholder="ابحث بالاسم أو الجهاز أو الموديل أو المشكلة...">
                    <x-slot:extra>
                        <div class="col-auto">
                            <label class="form-label small mb-1">طريقة الدفع</label>
                            <select name="payment_method" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                <option value="app" {{ request('payment_method') == 'app' ? 'selected' : '' }}>تطبيق</option>
                                <option value="mixed" {{ request('payment_method') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-1">الحالة</label>
                            <select name="is_returned" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                <option value="0" {{ request('is_returned') === '0' ? 'selected' : '' }}>غير مرجع</option>
                                <option value="1" {{ request('is_returned') === '1' ? 'selected' : '' }}>مرجع</option>
                            </select>
                        </div>
                    </x-slot:extra>
                    <x-slot:collapsible>
                        <div class="col-12">
                            <p class="text-muted small mb-2">فلترة إضافية حسب تاريخ التسليم — تُطبَّق بالإضافة إلى نطاق التاريخ أعلاه، وفقط على الصيانات التي لها تاريخ تسليم مسجَّل (أي الصيانات المكتملة فعلياً).</p>
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-1">من تاريخ التسليم</label>
                            <input type="date" name="delivery_date_from" value="{{ request('delivery_date_from') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-1">إلى تاريخ التسليم</label>
                            <input type="date" name="delivery_date_to" value="{{ request('delivery_date_to') }}" class="form-control form-control-sm">
                        </div>
                    </x-slot:collapsible>
                </x-filter-bar>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الجهاز</th>
                                <th>الموديل</th>
                                <th>المشكلة</th>
                                <th>الجوال</th>
                                <th>تاريخ الاستلام</th>
                                <th>الدفع</th>
                                <th>التكلفة نقدي</th>
                                <th>التكلفة بنكي</th>
                                <th>تاريخ التسليم</th>
                                <th>الموظف</th>
                                <th>مرجع</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                use Illuminate\Support\Str;
                            @endphp
                            @forelse($repairs as $repair)
                                <tr>
                                    <td>{{ $repair->id }}</td>
                                    <td>{{ $repair->customer_name }}</td>
                                    <td>{{ $repair->device_name }}</td>
                                    <td>{{ $repair->model }}</td>
                                    <td>{{ Str::limit($repair->issue, 30) }}</td>
                                    <td>{{ $repair->phone }}</td>
                                    <td>{{ optional($repair->received_date)->format('Y-m-d H:i') }}</td>
                                    <td><span class="badge bg-secondary">{{ $repair->payment_method }}</span></td>
                                    <td>{{ number_format($repair->cost_cash, 2) }}</td>
                                    <td>{{ number_format($repair->cost_bank, 2) }}</td>
                                    <td>{{ optional($repair->delivery_date)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $repair->received_by }}</td>
                                    <td>
                                        @if ($repair->is_returned)
                                            <span class="badge bg-danger">مرجع</span>
                                        @else
                                            <span class="badge bg-success">غير مرجع</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('repairs.edit', $repair) }}"
                                            class="btn btn-sm btn-outline-primary">تعديل</a>
                                        <form action="{{ route('repairs.destroy', $repair) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('تأكيد الحذف؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $repairs->links() }}
            </div>
        </div>
    </div>
@endsection
