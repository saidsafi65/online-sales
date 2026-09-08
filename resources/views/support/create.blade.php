@extends('layout.app')

@section('title', 'تقديم طلب دعم فني')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title"><i class="fas fa-headset"></i> تقديم طلب دعم فني</h1>
        <p class="welcome-subtitle">وصف الطلب بالتفصيل بيساعدنا نرد عليك بأسرع وقت</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('support.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">نوع الطلب</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">اختر النوع...</option>
                                @foreach (\App\Models\SupportTicket::TYPES as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">العنوان</label>
                            <input type="text" name="subject" id="subject" class="form-control"
                                placeholder="مثال: خطأ عند إضافة فاتورة جديدة" value="{{ old('subject') }}" maxlength="150" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">التفاصيل</label>
                            <textarea name="message" id="message" class="form-control" rows="6"
                                placeholder="اشرح المشكلة أو الطلب بالتفصيل — متى صارت، بأي صفحة، وأي خطوات نفّذتها" maxlength="3000" required>{{ old('message') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <a href="{{ route('support.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right"></i> رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> إرسال الطلب
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4" style="color: #64748b; font-size: 0.9rem;">
                لو الموضوع مستعجل، تقدر تتواصل مباشرة:
                <a href="tel:+970599971755" class="mx-1"><i class="fas fa-phone"></i> 0599971755</a>
                |
                <a href="https://wa.me/972599971755" target="_blank" rel="noopener" class="mx-1"><i class="fab fa-whatsapp"></i> واتساب</a>
            </div>
        </div>
    </div>
@endsection
