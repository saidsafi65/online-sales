@extends('layout.app')

@section('title', 'رفع نسخة احتياطية')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-upload"></i> رفع نسخة احتياطية
                    </h4>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>خطأ!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>تنبيه:</strong>
                        <ul class="mb-0 mt-2">
                            <li>يجب أن يكون الملف بصيغة <strong>.sql</strong></li>
                            <li>الحد الأقصى لحجم الملف: <strong>100 ميجابايت</strong></li>
                            <li>تأكد من أن النسخة الاحتياطية متوافقة مع قاعدة البيانات الحالية</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('backup.storeUpload') }}" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="mb-4">
                            <label for="backup_file" class="form-label">
                                <i class="fas fa-file-upload"></i> اختر ملف النسخة الاحتياطية
                            </label>
                            <input type="file" 
                                   class="form-control @error('backup_file') is-invalid @enderror" 
                                   id="backup_file" 
                                   name="backup_file"
                                   accept=".sql"
                                   required>
                            @error('backup_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                الملفات المدعومة: .sql (حتى 100 ميجابايت)
                            </small>
                        </div>

                        {{-- معلومات الملف المختار --}}
                        <div id="fileInfo" class="alert alert-info" style="display: none;">
                            <h6><i class="fas fa-file"></i> معلومات الملف:</h6>
                            <ul class="mb-0">
                                <li>الاسم: <strong id="fileName"></strong></li>
                                <li>الحجم: <strong id="fileSize"></strong></li>
                                <li>النوع: <strong id="fileType"></strong></li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="uploadBtn">
                                <i class="fas fa-upload"></i> رفع النسخة الاحتياطية
                            </button>
                            
                            <a href="{{ route('backup.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>

                        <div id="loadingDiv" style="display: none;" class="text-center mt-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">جاري الرفع...</span>
                            </div>
                            <p class="mt-3 text-muted">جاري رفع الملف... الرجاء الانتظار</p>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 0%"
                                     id="uploadProgress">0%</div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt"></i> سيتم حفظ الملف في: <code>storage/app/backups</code>
                    </small>
                </div>
            </div>

            {{-- معلومات إضافية --}}
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-question-circle text-info"></i> كيفية الحصول على نسخة احتياطية من phpMyAdmin
                    </h5>
                    <ol>
                        <li>افتح phpMyAdmin من متصفح الويب</li>
                        <li>اختر قاعدة البيانات المطلوبة من القائمة اليسرى</li>
                        <li>اضغط على تبويب "تصدير" (Export)</li>
                        <li>اختر طريقة التصدير: "سريع" (Quick) أو "مخصص" (Custom)</li>
                        <li>تأكد من اختيار صيغة SQL</li>
                        <li>اضغط على زر "تنفيذ" (Go)</li>
                        <li>سيتم تحميل ملف .sql يمكنك رفعه هنا</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// عرض معلومات الملف المختار
document.getElementById('backup_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileInfo').style.display = 'block';
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatBytes(file.size);
        document.getElementById('fileType').textContent = file.type || 'application/sql';
    }
});

// تنسيق حجم الملف
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// معالجة رفع الملف
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('loadingDiv').style.display = 'block';
    
    // محاكاة شريط التقدم (في الواقع يجب استخدام XMLHttpRequest للحصول على التقدم الحقيقي)
    let progress = 0;
    const progressBar = document.getElementById('uploadProgress');
    const interval = setInterval(function() {
        if (progress < 90) {
            progress += 10;
            progressBar.style.width = progress + '%';
            progressBar.textContent = progress + '%';
        } else {
            clearInterval(interval);
        }
    }, 300);
});
</script>
@endpush