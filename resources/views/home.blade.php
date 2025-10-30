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
                    <p class="service-description">إدارة عمليات البيع اليومية، عرض السجل الكامل للمبيعات، وتتبع الإحصائيات
                        والدخل</p>
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
        <!-- التوافقات -->
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('compatibility.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="service-title">التوافقات</h3>
                    <p class="service-description">إدارة توافق الأجهزة، إضافة قطع غيار، وتتبع التوافقات</p>
                </div>
            </a>
        </div>
        <!-- طلبات الزبائن -->
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('customer-orders.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-box"></i> <!-- يمكنك استخدام أي أيقونة تناسب طلبات الزبائن -->
                    </div>
                    <h3 class="service-title">طلبات الزبائن</h3>
                    <p class="service-description">إدارة طلبات الزبائن، إضافة طلب جديد، وتعديل الطلبات</p>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('daily-handovers.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-handshake"></i> <!-- استخدم أي أيقونة تناسب عملية التسليم اليومي -->
                    </div>
                    <h3 class="service-title">التسليمات اليومية</h3>
                    <p class="service-description">إدارة التسليمات اليومية، إضافة تسليم جديد، وتعديل التسليمات</p>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('returned-goods.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-undo-alt"></i> <!-- استخدم أي أيقونة تناسب البضائع المرجعة -->
                    </div>
                    <h3 class="service-title">البضائع المرجعة</h3>
                    <p class="service-description">إدارة البضائع المرجعة، إضافة بضاعة مرجعة جديدة، وتعديل السجلات الحالية
                    </p>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('store.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-arrow-up"></i> <!-- استخدم الأيقونة التي تناسب الوضع -->
                    </div>
                    <h3 class="service-title">البضائع في المخزن الخارجي</h3>
                    <p class="service-description">إدارة البضائع في المخزن، إضافة بضاعة جديدة، وتعديل السجلات الحالية.</p>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('debts.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-hand-holding-usd"></i> <!-- أيقونة تناسب الديون مثل المال -->
                    </div>
                    <h3 class="service-title">إدارة الديون</h3>
                    <p class="service-description">إدارة سجلات الديون، إضافة دين جديد، وتعديل السجلات الحالية.</p>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('backup.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-database"></i> <!-- أيقونة تناسب النسخ الاحتياطي مثل قاعدة البيانات -->
                    </div>
                    <h3 class="service-title">إدارة النسخ الاحتياطي</h3>
                    <p class="service-description">إدارة النسخ الاحتياطي للبيانات، إنشاء نسخ جديدة، تحميل واستعادة النسخ.
                    </p>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('maintenance_parts.index') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-cogs"></i> <!-- أيقونة تناسب قطع الصيانة -->
                    </div>
                    <h3 class="service-title">إدارة قطع الصيانة</h3>
                    <p class="service-description">
                        إدارة قطع الصيانة، إضافة قطع جديدة، تعديل أو حذف قطع الصيانة.
                    </p>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('products.index-admin') }}" class="text-decoration-none">
                <div class="service-card card-info">
                    <div class="service-icon">
                        <i class="fas fa-boxes"></i> <!-- أيقونة تناسب المنتجات -->
                    </div>
                    <h3 class="service-title">إدارة المنتجات</h3>
                    <p class="service-description">
                        إدارة المنتجات، إضافة منتجات جديدة، تعديل أو حذف المنتجات.
                    </p>
                </div>
            </a>
        </div>


    </div>

    <!-- Quick Stats Section -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div
                style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">
                <h4
                    style="color: #1e293b; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-chart-bar" style="color: #475569;"></i> نظرة سريعة
                </h4>

                <div class="row g-4 text-center">

                    <!-- صافي دخل الشهر -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; border: 1px solid #bbf7d0;">
                            <div style="font-size: 2rem; font-weight: 800; color: #16a34a; margin-bottom: 0.5rem;">
                                {{ number_format($monthlyRevenue ?? 0) }} شيكل
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">صافي الشهر</div>
                        </div>
                    </div>

                    <!-- مبيعات الشهر -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 12px; border: 1px solid #bfdbfe;">
                            <div style="font-size: 2rem; font-weight: 800; color: #2563eb; margin-bottom: 0.5rem;">
                                {{ number_format($monthlyIncome ?? 0) }} شيكل
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">مبيعات الشهر مع الصيانة</div>
                        </div>
                    </div>

                    <!-- مصروفات الشهر -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: 12px; border: 1px solid #fecaca;">
                            <div style="font-size: 2rem; font-weight: 800; color: #dc2626; margin-bottom: 0.5rem;">
                                {{ number_format($totalMonthlyPurchases ?? 0) }} شيكل
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">مصروفات الشهر مع الالتزامات
                            </div>
                        </div>
                    </div>

                    <!-- الديون المتراكمة -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border-radius: 12px; border: 1px solid #fde047;">
                            <div style="font-size: 1.5rem; font-weight: 800; color: #ca8a04; margin-bottom: 0.5rem;">
                                <div>علينا: {{ number_format($totalReceivables ?? 0) }} شيكل</div>
                                <div>لنا: {{ number_format($totalPayables ?? 0) }} شيكل</div>
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">الديون المتراكمة</div>
                        </div>
                    </div>

                    <!-- مبيعات اليوم -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; border: 1px solid #cbd5e1;">
                            <div style="font-size: 2rem; font-weight: 800; color: #475569; margin-bottom: 0.5rem;">
                                {{ $todaySales ?? '0' }}
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">مبيعات اليوم</div>
                        </div>
                    </div>

                    <!-- صيانات معلقة -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 12px; border: 1px solid #fed7aa;">
                            <div style="font-size: 2rem; font-weight: 800; color: #ea580c; margin-bottom: 0.5rem;">
                                {{ $pendingRepairs ?? '0' }}
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">صيانات معلقة</div>
                        </div>
                    </div>

                    <!-- إجمالي المنتجات -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border-radius: 12px; border: 1px solid #ddd6fe;">
                            <div style="font-size: 2rem; font-weight: 800; color: #7c3aed; margin-bottom: 0.5rem;">
                                {{ $totalProducts ?? '0' }}
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">إجمالي المنتجات</div>
                        </div>
                    </div>

                    <!-- إجمالي العملاء -->
                    <div class="col-6 col-md-3">
                        <div
                            style="padding: 1.5rem; background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%); border-radius: 12px; border: 1px solid #f5d0fe;">
                            <div style="font-size: 2rem; font-weight: 800; color: #c026d3; margin-bottom: 0.5rem;">
                                {{ $totalCustomers ?? '0' }}
                            </div>
                            <div style="color: #64748b; font-weight: 600; font-size: 0.9rem;">إجمالي العملاء</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
