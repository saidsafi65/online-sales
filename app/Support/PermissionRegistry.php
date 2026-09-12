<?php

namespace App\Support;

class PermissionRegistry
{
    private const STANDARD = [
        'view' => 'عرض',
        'create' => 'إضافة',
        'edit' => 'تعديل',
        'delete' => 'حذف',
    ];

    /**
     * كل قسم بالموقع مع اسمه بالعربي ومجموعة الصلاحيات المتاحة له.
     * المفاتيح المخزّنة فعلياً بعمود permissions هي "module.action"، مثلاً "sales.view".
     */
    public static function all(): array
    {
        return [
            'sales' => ['label' => 'المبيعات', 'actions' => self::STANDARD],
            'repairs' => ['label' => 'الصيانة', 'actions' => self::STANDARD + ['send_sms' => 'إرسال رسالة SMS']],
            'purchases' => ['label' => 'المشتريات', 'actions' => self::STANDARD],
            'catalog' => ['label' => 'كتالوج المنتجات', 'actions' => self::STANDARD],
            'deposits' => ['label' => 'أمانات الصيانة', 'actions' => self::STANDARD],
            'reports' => ['label' => 'التقارير', 'actions' => ['view' => 'عرض']],
            'obligations' => ['label' => 'التزامات المحل الشهرية', 'actions' => self::STANDARD],
            // لا يوجد إجراء "تعديل" فعلي لهذه المستندات الأربعة (تُنشأ ثم إما تُحذف أو تُسجَّل عليها دفعات)
            'invoices' => ['label' => 'الفواتير', 'actions' => ['view' => 'عرض', 'create' => 'إضافة', 'delete' => 'حذف']],
            'financial_claims' => ['label' => 'المطالبات المالية', 'actions' => ['view' => 'عرض', 'create' => 'إضافة', 'delete' => 'حذف']],
            'wholesale_invoices' => ['label' => 'فواتير الجملة', 'actions' => ['view' => 'عرض', 'create' => 'إضافة', 'delete' => 'حذف', 'send_reminder' => 'إرسال تذكير SMS']],
            'price_quotes' => ['label' => 'عروض الأسعار', 'actions' => ['view' => 'عرض', 'create' => 'إضافة', 'delete' => 'حذف', 'convert_to_invoice' => 'تحويل لفاتورة']],
            'compatibility' => ['label' => 'التوافقات', 'actions' => ['view' => 'عرض', 'create' => 'إضافة', 'delete' => 'حذف']],
            'customer_orders' => ['label' => 'طلبات الزبائن', 'actions' => self::STANDARD],
            'daily_handovers' => ['label' => 'التسليمات اليومية', 'actions' => self::STANDARD],
            'returned_goods' => ['label' => 'البضائع المرجعة', 'actions' => self::STANDARD],
            'store' => ['label' => 'المخزن الخارجي', 'actions' => self::STANDARD],
            'debts' => ['label' => 'الديون', 'actions' => self::STANDARD + ['send_reminder' => 'إرسال تذكير SMS']],
            'backup' => ['label' => 'النسخ الاحتياطي', 'actions' => ['view' => 'عرض', 'create' => 'إنشاء نسخة', 'restore' => 'استعادة نسخة', 'delete' => 'حذف نسخة']],
            'maintenance_parts' => ['label' => 'قطع الصيانة', 'actions' => self::STANDARD],
            'products' => ['label' => 'إدارة المنتجات (منتجات/لابتوبات/برامج)', 'actions' => self::STANDARD],
            'online_orders' => ['label' => 'الطلبات الإلكترونية', 'actions' => ['view' => 'عرض', 'edit' => 'تغيير الحالة']],
            'community' => ['label' => 'مجتمع المعارض', 'actions' => ['view' => 'عرض']],
            'mobile_shop' => ['label' => 'معرض الجوال', 'actions' => self::STANDARD],
            'activity_log' => ['label' => 'سجل النشاطات', 'actions' => ['view' => 'عرض']],
            'sms_log' => ['label' => 'سجل الرسائل المرسلة', 'actions' => ['view' => 'عرض']],
            'users' => ['label' => 'إدارة الموظفين', 'actions' => ['view' => 'عرض', 'create' => 'إضافة', 'edit' => 'تعديل']],
            'branches' => ['label' => 'إدارة الفروع', 'actions' => self::STANDARD],
            'branding' => ['label' => 'هوية المعرض', 'actions' => ['view' => 'عرض', 'edit' => 'تعديل']],
            'payment_gateways' => ['label' => 'طرق الدفع', 'actions' => ['view' => 'عرض', 'edit' => 'تعديل']],
            'coupons' => ['label' => 'أكواد الخصم', 'actions' => self::STANDARD],
        ];
    }

    public static function actionsFor(string $module): array
    {
        return self::all()[$module]['actions'] ?? [];
    }

    /** @return string[] */
    public static function fullGrantFor(string $module): array
    {
        return array_map(
            fn (string $action) => "{$module}.{$action}",
            array_keys(self::actionsFor($module))
        );
    }

    public static function isValidKey(string $key): bool
    {
        [$module, $action] = array_pad(explode('.', $key, 2), 2, null);

        return $module !== null && $action !== null && array_key_exists($action, self::actionsFor($module));
    }
}
