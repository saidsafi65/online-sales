@extends('layout.gust')

@section('title', 'شروط الاستخدام')

@push('styles')
@include('legal._styles')
@endpush

@section('content')
<div class="legal-hero">
    <i class="fas fa-file-contract"></i>
    <h1>شروط الاستخدام</h1>
    <p>القواعد اللي بتحكم استخدامك لهاد الموقع</p>
</div>

<div class="legal-wrap">
    <div class="legal-card">
        <section>
            <h2><i class="fas fa-store"></i> الملكية</h2>
            <p>
                موقع "Online Sale" مشروع وملكية شخصية بالكامل للمهندس <strong>سعيد محمد صافي</strong>،
                وهو المالك الوحيد والمسؤول الوحيد عن الموقع بكل محتوياته وأقسامه (المنتجات، أجهزة اللابتوب،
                البرامج، ولوحة التحكم)، وما إله علاقة بأي شركة أو جهة أو شخص تاني.
            </p>
            <span class="legal-owner-badge"><i class="fas fa-certificate"></i> ملكية شخصية 100%</span>
        </section>

        <section>
            <h2><i class="fas fa-check-circle"></i> الاستخدام المقبول</h2>
            <p>باستخدامك لهاد الموقع، بتوافق إنك:</p>
            <ul>
                <li>ما تحاول الدخول غير المصرح فيه للوحة التحكم أو أي بيانات مو مخصصة إلك</li>
                <li>ما تستخدم الموقع بأي طريقة ممكن تضر بعمله أو أمانه (اختراق، سحب بيانات آلي، إلخ)</li>
                <li>ما تعيد استخدام أو نسخ محتوى الموقع (تصاميم، أكواد، بيانات المنتجات) لأغراض تجارية بدون إذن</li>
            </ul>
        </section>

        <section>
            <h2><i class="fas fa-tags"></i> المنتجات والأسعار</h2>
            <ul>
                <li>الأسعار المعروضة بالشيكل (₪) وقابلة للتغيير بأي وقت بدون إشعار مسبق</li>
                <li>توفر المنتجات (منتجات، لابتوبات، برامج) غير مضمون وممكن يتغير حسب المخزون</li>
                <li>بنحاول نعرض معلومات دقيقة قدر الإمكان، بس ممكن يصير أخطاء بسيطة بالوصف أو السعر بدون قصد</li>
            </ul>
        </section>

        <section>
            <h2><i class="fas fa-copyright"></i> الملكية الفكرية</h2>
            <p>
                كل شي بالموقع (التصميم، الكود، الشعار، طريقة عرض المحتوى) هو ملكية خاصة لصاحب الموقع، ومش
                مسموح إعادة نشره أو نسخه أو استخدامه بدون إذن كتابي مسبق.
            </p>
        </section>

        <section>
            <h2><i class="fas fa-triangle-exclamation"></i> إخلاء المسؤولية</h2>
            <p>
                الموقع مقدَّم "كما هو" بدون أي ضمانات. بنبذل قصارى جهدنا نخلي الموقع شغال ودقيق، بس مش مسؤولين
                عن أي أضرار غير مباشرة ممكن تنتج عن استخدام الموقع أو انقطاعه أو أي خطأ تقني فيه.
            </p>
        </section>

        <section>
            <h2><i class="fas fa-rotate"></i> التعديل على الشروط</h2>
            <p>
                صاحب الموقع محتفظ بحقه بتعديل شروط الاستخدام هاي بأي وقت، وبتصير التعديلات سارية فور نشرها
                بهاي الصفحة.
            </p>
        </section>

        <section>
            <h2><i class="fas fa-envelope-open-text"></i> للتواصل</h2>
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
