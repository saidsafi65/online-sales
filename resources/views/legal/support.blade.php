@extends('layout.gust')

@section('title', 'الدعم الفني')

@push('styles')
@include('legal._styles')
@endpush

@section('content')
<div class="legal-hero">
    <i class="fas fa-headset"></i>
    <h1>الدعم الفني</h1>
    <p>هون بتلاقي كل طرق التواصل إذا واجهتك أي مشكلة أو كان عندك استفسار</p>
</div>

<div class="legal-wrap">
    <div class="legal-card">
        <section>
            <h2><i class="fas fa-user"></i> مين بيرد عليك</h2>
            <p>
                هاد الموقع مشروع شخصي بالكامل، وصاحبه ومسؤول الدعم الفني عليه هو
                <strong>المهندس سعيد محمد صافي</strong>. أي استفسار أو مشكلة بترسلها بتوصلني أنا شخصيًا.
            </p>
        </section>

        <section>
            <h2><i class="fas fa-comments"></i> طرق التواصل</h2>
            <div class="legal-contact-box">
                <a href="tel:+970599971755" class="legal-contact-item">
                    <i class="fas fa-phone"></i> 0599971755
                </a>
                <a href="https://wa.me/972599971755" target="_blank" rel="noopener" class="legal-contact-item">
                    <i class="fab fa-whatsapp"></i> واتساب
                </a>
                <a href="mailto:said.safi.056@gmail.com" class="legal-contact-item">
                    <i class="fas fa-envelope"></i> said.safi.056@gmail.com
                </a>
            </div>
        </section>

        <section>
            <h2><i class="fas fa-list-check"></i> شو بقدر أساعدك فيه</h2>
            <ul>
                <li>مشاكل بتسجيل الدخول أو الوصول لحسابك</li>
                <li>استفسارات عن المنتجات، أجهزة اللابتوب، أو البرامج المعروضة</li>
                <li>الإبلاغ عن خطأ تقني أو مشكلة بأي صفحة بالموقع</li>
                <li>أي اقتراح لتحسين الموقع</li>
            </ul>
        </section>

        <section>
            <h2><i class="fas fa-clock"></i> وقت الرد</h2>
            <p>
                بما إنه هاد مشروع شخصي وما في فريق دعم فني رسمي، الرد بصير خلال أقرب وقت ممكن حسب توفري،
                وغالبًا بيكون أسرع عبر الواتساب أو الاتصال المباشر.
            </p>
        </section>
    </div>
</div>
@endsection
