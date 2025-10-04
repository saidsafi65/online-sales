@extends('layout.app')

@section('title', 'الصفحة الرئيسية')

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <h1 class="welcome-title">مرحباً بك في نظام إدارة المبيعات</h1>
    <p class="welcome-subtitle">اختر القسم الذي تود الدخول إليه لإدارة عملك بكل سهولة واحترافية</p>
</div>

<!-- Services Grid -->
<div class="row g-4 justify-content-center">
    <!-- المبيعات -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('sales.index') }}" class="text-decoration-none">
            <div class="service-card card-primary">
                <div class="service-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="service-title">المبيعات</h3>
                <p class="service-description">إدارة عمليات البيع اليومية، عرض السجل الكامل للمبيعات، وتتبع الإحصائيات والدخل</p>
            </div>
        </a>
    </div>

    <!-- الصيانة -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('repairs.index') }}" class="text-decoration-none">
            <div class="service-card card-warning">
                <div class="service-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 class="service-title">الصيانة</h3>
                <p class="service-description">إدارة طلبات الصيانة، متابعة العملاء، وسجل كامل لجميع عمليات الإصلاح</p>
            </div>
        </a>
    </div>

    <!-- المشتريات -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('purchases.index') }}" class="text-decoration-none">
            <div class="service-card card-success">
                <div class="service-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="service-title">المشتريات</h3>
                <p class="service-description">تسجيل وإدارة المشتريات، متابعة الموردين، وتنظيم عمليات الشراء</p>
            </div>
        </a>
    </div>

    <!-- الكتالوج -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('catalog.index') }}" class="text-decoration-none">
            <div class="service-card card-info">
                <div class="service-icon">
                    <i class="fas fa-list"></i>
                </div>
                <h3 class="service-title">كتالوج المنتجات</h3>
                <p class="service-description">إضافة وإدارة أسماء المنتجات، الأنواع، والفئات المختلفة</p>
            </div>
        </a>
    </div>

    <!-- أمانات الصيانة -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('deposits.index') }}" class="text-decoration-none">
            <div class="service-card card-purple">
                <div class="service-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="service-title">أمانات الصيانة</h3>
                <p class="service-description">إدارة الأمانات والقطع المأخوذة خلال عمليات الصيانة وتتبعها</p>
            </div>
        </a>
    </div>

    <!-- التقارير -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('reports.index') }}" class="text-decoration-none">
            <div class="service-card card-info">
                <div class="service-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="service-title">التقارير والإحصائيات</h3>
                <p class="service-description">عرض التقارير التفصيلية، الإحصائيات الشاملة، وتحليل الأداء</p>
            </div>
        </a>
    </div>

        <!-- التزامات المحل الشهرية -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('obligations.index') }}" class="text-decoration-none">
            <div class="service-card card-primary">
                <div class="service-icon">
                    <i class="fas fa-clipboard-list"></i> <!-- يمكنك تغيير الأيقونة هنا -->
                </div>
                <h3 class="service-title">التزامات المحل الشهرية</h3>
                <p class="service-description">إدارة التزامات المحل الشهرية مثل الرواتب والإيجار والمصروفات الشهرية</p>
            </div>
        </a>
    </div>
    <!-- الفواتير -->
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="{{ route('invoices.index') }}" class="text-decoration-none">
            <div class="service-card card-info">
                <div class="service-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h3 class="service-title">الفواتير</h3>
                <p class="service-description">عرض الفواتير، تتبع المدفوعات، وإدارة الحسابات</p>
            </div>
        </a>
    </div>


</div>

<!-- Quick Stats Section (Optional) -->
<div class="row g-4 mt-5">
    <div class="col-12">
        <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);">
            <h4 style="color: #1e293b; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-chart-bar" style="color: #1e40af;"></i> نظرة سريعة
            </h4>

            <div class="row g-4 text-center">

                <!-- دخل الشهر -->
                <div class="col-6 col-md-3">
                    <div style="padding: 1.5rem; background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border-radius: 15px;">
                        <div style="font-size: 2rem; font-weight: 900; color: #0ea5e9; margin-bottom: 0.5rem;">
                            {{ $monthlyRevenue ?? '0' }} ₪
                        </div>
                        <div style="color: #64748b; font-weight: 500;">دخل الشهر</div>
                    </div>
                </div>

                <!-- مبيعات اليوم -->
                <div class="col-6 col-md-3">
                    <div style="padding: 1.5rem; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 15px;">
                        <div style="font-size: 2rem; font-weight: 900; color: #1e40af; margin-bottom: 0.5rem;">
                            {{ $todaySales ?? '0' }}
                        </div>
                        <div style="color: #64748b; font-weight: 500;">مبيعات اليوم</div>
                    </div>
                </div>

                <!-- صيانات معلقة -->
                <div class="col-6 col-md-3">
                    <div style="padding: 1.5rem; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 15px;">
                        <div style="font-size: 2rem; font-weight: 900; color: #f59e0b; margin-bottom: 0.5rem;">
                            {{ $pendingRepairs ?? '0' }}
                        </div>
                        <div style="color: #64748b; font-weight: 500;">صيانات معلقة</div>
                    </div>
                </div>

                <!-- إجمالي المنتجات -->
                <div class="col-6 col-md-3">
                    <div style="padding: 1.5rem; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 15px;">
                        <div style="font-size: 2rem; font-weight: 900; color: #10b981; margin-bottom: 0.5rem;">
                            {{ $totalProducts ?? '0' }}
                        </div>
                        <div style="color: #64748b; font-weight: 500;">إجمالي المنتجات</div>
                    </div>
                </div>

                <!-- إجمالي العملاء -->
                <div class="col-6 col-md-3">
                    <div style="padding: 1.5rem; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); border-radius: 15px;">
                        <div style="font-size: 2rem; font-weight: 900; color: #8b5cf6; margin-bottom: 0.5rem;">
                            {{ $totalCustomers ?? '0' }}
                        </div>
                        <div style="color: #64748b; font-weight: 500;">إجمالي العملاء</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
