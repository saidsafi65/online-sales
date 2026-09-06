@extends('layout.app')

@section('title', 'إنشاء نسخة احتياطية')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-download"></i> إنشاء نسخة احتياطية جديدة
                    </h4>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>معلومات:</strong>
                        <ul class="mb-0 mt-2">
                            <li>سيتم إنشاء نسخة احتياطية كاملة من قاعدة البيانات</li>
                            <li>اسم قاعدة البيانات: <strong>{{ config('database.connections.mysql.database') }}</strong></li>
                            <li>سيتم حفظ الملف في: <code>storage/app/backups</code></li>
                            <li>صيغة اسم الملف: <code>backup_YYYY-MM-DD_HH-MM-SS.sql</code></li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('backup.store') }}" id="backupForm">
                        @csrf

                        <div class="text-center py-4">
                            <i class="fas fa-database fa-5x text-primary mb-4"></i>
                            <h5 class="mb-4">هل أنت مستعد لإنشاء نسخة احتياطية؟</h5>
                            
                            <button type="submit" class="btn btn-primary btn-lg" id="createBackupBtn">
                                <i class="fas fa-check"></i> إنشاء النسخة الاحتياطية الآن
                            </button>
                            
                            <a href="{{ route('backup.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>

                        <div id="loadingDiv" style="display: none;" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري المعالجة...</span>
                            </div>
                            <p class="mt-3 text-muted">جاري إنشاء النسخة الاحتياطية... الرجاء الانتظار</p>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> قد تستغرق العملية بضع دقائق حسب حجم قاعدة البيانات
                        </small>
                        <a href="{{ route('backup.index') }}" class="btn btn-link btn-sm">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>

            {{-- بطاقة معلومات إضافية --}}
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb text-warning"></i> نصائح
                    </h5>
                    <ul class="mb-0">
                        <li>قم بإنشاء نسخ احتياطية بشكل دوري (يومي، أسبوعي، أو شهري)</li>
                        <li>احتفظ بنسخ احتياطية متعددة من فترات زمنية مختلفة</li>
                        <li>تأكد من تخزين النسخ الاحتياطية في مكان آمن خارج السيرفر</li>
                        <li>اختبر النسخ الاحتياطية للتأكد من إمكانية استعادتها</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('backupForm').addEventListener('submit', function() {
    document.getElementById('createBackupBtn').style.display = 'none';
    document.getElementById('loadingDiv').style.display = 'block';
});
</script>
@endpush