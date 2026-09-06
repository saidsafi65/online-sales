@extends('layout.gust')

@section('title', 'سياسة الخصوصية')

@push('styles')
@include('legal._styles')
@endpush

@section('content')
<div class="legal-hero">
    <i class="fas fa-shield-halved"></i>
    <h1>سياسة الخصوصية</h1>
    <p>كيف بنتعامل مع بياناتك وشو بنجمع منها وليش</p>
</div>

<div class="legal-wrap">
    <div class="legal-card">
        <section>
            <h2><i class="fas fa-store"></i> عن هاد الموقع</h2>
            <p>
                "Online Sale" مشروع شخصي بالكامل، مملوك ومُدار من طرف واحد فقط هو
                <strong>المهندس سعيد محمد صافي</strong>، وهو الجهة الوحيدة المسؤولة عن الموقع ومحتواه
                وبياناته. الموقع مش ملك أو تابع لأي شركة أو جهة أو شخص آخر.
            </p>
            <span class="legal-owner-badge"><i class="fas fa-certificate"></i> ملكية شخصية 100%</span>
        </section>

        <section>
            <h2><i class="fas fa-database"></i> شو البيانات اللي بنجمعها</h2>
            <ul>
                <li>بيانات حسابات الموظفين المصرّح لهم بالدخول للوحة التحكم (اسم، بريد إلكتروني، صلاحيات)</li>
                <li>سجلات المبيعات والفواتير اللي بتنعمل عبر النظام لغرض إدارة المحل</li>
                <li>ملفات تعريف الارتباط (Cookies) الأساسية لإبقاء تسجيل الدخول شغال أثناء استخدامك للوحة التحكم</li>
            </ul>
            <p>الموقع بالجزء العام (تصفح المنتجات) ما بيطلب منك أي بيانات شخصية عشان تتصفح.</p>
        </section>

        <section>
            <h2><i class="fas fa-eye"></i> ليش بنستخدم هاي البيانات</h2>
            <ul>
                <li>تشغيل النظام الداخلي لإدارة المبيعات والمخزون والفواتير</li>
                <li>الحفاظ على أمان لوحة التحكم ومنع الدخول غير المصرح فيه</li>
                <li>التواصل معك إذا تواصلت أنت معنا أول (عبر الدعم الفني)</li>
            </ul>
        </section>

        <section>
            <h2><i class="fas fa-user-shield"></i> مشاركة البيانات</h2>
            <p>
                البيانات ما بتنباع ولا بتنشارك مع أي طرف ثالث لأي غرض تجاري أو إعلاني. البيانات بتضل محصورة
                باستخدام النظام الداخلي فقط تحت مسؤولية صاحب الموقع.
            </p>
        </section>

        <section>
            <h2><i class="fas fa-lock"></i> أمان البيانات</h2>
            <p>
                بيتم اتخاذ إجراءات تقنية معقولة (زي كلمات مرور مشفّرة وصلاحيات دخول محدودة) لحماية البيانات
                المخزّنة قدر الإمكان، مع العلم إنه ما في نظام مضمون 100% من أي اختراق.
            </p>
        </section>

        <section>
            <h2><i class="fas fa-rotate"></i> تحديث السياسة</h2>
            <p>
                ممكن تنعمل تحديثات على سياسة الخصوصية هاي بأي وقت حسب تطور الموقع، وبتصير التحديثات سارية
                فور نشرها بهاي الصفحة.
            </p>
        </section>

        <section>
            <h2><i class="fas fa-envelope-open-text"></i> للتواصل بخصوص الخصوصية</h2>
            <div class="legal-contact-box">
                <a href="tel:+970599971755" class="legal-contact-item">
                    <i class="fas fa-phone"></i> 0599971755
                </a>
                <a href="mailto:said.safi.056@gmail.com" class="legal-contact-item">
                    <i class="fas fa-envelope"></i> said.safi.056@gmail.com
                </a>
            </div>
        </section>

        <div class="legal-updated">آخر تحديث: {{ now()->translatedFormat('d F Y') }}</div>
    </div>
</div>
@endsection
