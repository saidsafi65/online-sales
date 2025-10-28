@extends('layout.app')

@section('title', 'النسخ الاحتياطية')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-database"></i> إدارة النسخ الاحتياطية
                        </h4>
                        <div>
                            <a href="{{ route('backup.create') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-plus"></i> إنشاء نسخة جديدة
                            </a>
                            <a href="{{ route('backup.upload') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-upload"></i> رفع نسخة احتياطية
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- رسائل النجاح والخطأ --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- جدول النسخ الاحتياطية --}}
                        @if (count($backups) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="35%">اسم الملف</th>
                                            <th width="15%">الحجم</th>
                                            <th width="20%">تاريخ الإنشاء</th>
                                            <th width="25%" class="text-center">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($backups as $index => $backup)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <i class="fas fa-file-archive text-primary"></i>
                                                    {{ $backup['name'] }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $backup['size'] }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar"></i>
                                                        {{ $backup['date'] }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    {{-- تحميل --}}
                                                    <a href="{{ route('backup.download', $backup['name']) }}"
                                                        class="btn btn-success btn-sm" title="تحميل">
                                                        <i class="fas fa-download"></i>
                                                    </a>

                                                    {{-- استعادة --}}
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="confirmRestore('{{ $backup['name'] }}')" title="استعادة">
                                                        <i class="fas fa-undo"></i>
                                                    </button>

                                                    {{-- حذف --}}
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete('{{ $backup['name'] }}')" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد نسخ احتياطية حالياً</h5>
                                <p class="text-muted">قم بإنشاء نسخة احتياطية جديدة أو رفع نسخة موجودة</p>
                                <div class="mt-3">
                                    <a href="{{ route('backup.create') }}" class="btn btn-primary me-2">
                                        <i class="fas fa-plus"></i> إنشاء نسخة جديدة
                                    </a>
                                    <a href="{{ route('backup.upload') }}" class="btn btn-secondary">
                                        <i class="fas fa-upload"></i> رفع نسخة احتياطية
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- معلومات إضافية --}}
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle text-info"></i> ملاحظات هامة
                        </h5>
                        <ul class="mb-0">
                            <li>يتم حفظ النسخ الاحتياطية في المجلد: <code>storage/app/backups</code></li>
                            <li>تأكد من وجود أداة <code>mysqldump</code> و <code>mysql</code> مثبتة على السيرفر</li>
                            <li>عملية الاستعادة ستقوم بحذف جميع البيانات الحالية واستبدالها بالنسخة الاحتياطية</li>
                            <li>يُنصح بإنشاء نسخة احتياطية قبل القيام بأي تحديثات كبيرة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- نماذج التأكيد --}}
    <form id="restoreForm" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script>
        function confirmRestore(filename) {
            if (confirm('تحذير! سيتم حذف جميع البيانات الحالية واستعادة النسخة الاحتياطية. هل أنت متأكد؟')) {
                const form = document.getElementById('restoreForm');
                form.action = "{{ route('backup.restore', ':filename') }}".replace(':filename', filename);
                form.submit();
            }
        }

        function confirmDelete(filename) {
            if (confirm('هل أنت متأكد من حذف هذه النسخة الاحتياطية؟')) {
                const form = document.getElementById('deleteForm');
                form.action = "{{ route('backup.destroy', ':filename') }}".replace(':filename', filename);
                form.submit();
            }
        }
    </script>
@endpush
