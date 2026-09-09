<?php

namespace App\Support;

class ActivityLogFormatter
{
    /**
     * أسماء الموديلات بالعربي، للعرض بدل الاسم التقني الكامل (App\Models\Sale).
     */
    protected array $modelLabels = [
        'App\\Models\\Sale' => 'مبيعة',
        'App\\Models\\MobileSale' => 'مبيعة معرض الجوال',
        'App\\Models\\Repair' => 'صيانة',
        'App\\Models\\Purchase' => 'مشترى',
        'App\\Models\\CatalogItem' => 'كتالوج',
        'App\\Models\\Product' => 'منتج',
        'App\\Models\\SaleLaptop' => 'لابتوب',
        'App\\Models\\Obligation' => 'التزام مالي',
        'App\\Models\\Coupon' => 'كوبون خصم',
        'App\\Models\\Order' => 'طلب أونلاين',
        'App\\Models\\Debt' => 'دين',
        'App\\Models\\ReturnedGood' => 'مرتجع',
        'App\\Models\\Invoice' => 'فاتورة',
        'App\\Models\\InvoicePayment' => 'دفعة فاتورة',
        'App\\Models\\FinancialClaim' => 'مطالبة مالية',
        'App\\Models\\DailyHandover' => 'تسليم يومي',
        'App\\Models\\MaintenancePart' => 'قطعة صيانة',
        'App\\Models\\MaintenanceDeposit' => 'عربون صيانة',
        'App\\Models\\Customer' => 'عميل',
        'App\\Models\\User' => 'مستخدم',
        'App\\Models\\Branch' => 'فرع',
        'App\\Models\\MobileInventory' => 'مخزون معرض الجوال',
        'App\\Models\\Software' => 'برنامج',
        'App\\Models\\CustomerOrder' => 'طلب عميل',
    ];

    protected array $actionLabels = [
        'created' => 'إضافة',
        'updated' => 'تعديل',
        'deleted' => 'حذف',
    ];

    protected array $fieldLabels = [
        'quantity' => 'الكمية',
        'cash_amount' => 'المبلغ النقدي',
        'app_amount' => 'مبلغ الشبكة/التطبيق',
        'amount_cash' => 'المبلغ النقدي',
        'amount_bank' => 'المبلغ البنكي',
        'cost_cash' => 'التكلفة نقدي',
        'cost_bank' => 'التكلفة بنكي',
        'product' => 'المنتج',
        'type' => 'النوع',
        'price' => 'السعر',
        'sale_price' => 'سعر البيع',
        'purchase_price' => 'سعر الشراء',
        'customer_name' => 'اسم الزبون',
        'device_name' => 'الجهاز',
        'status' => 'الحالة',
        'notes' => 'ملاحظات',
        'is_returned' => 'مرتجع',
        'payment_method' => 'طريقة الدفع',
        'supplier_name' => 'المورد',
        'branch_id' => 'الفرع',
        'name' => 'الاسم',
        'description' => 'الوصف',
        'discount_amount' => 'الخصم',
        'total_amount' => 'الإجمالي',
        'total_price' => 'الإجمالي',
        'cost' => 'التكلفة',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'sale_date' => 'تاريخ البيع',
        'purchase_date' => 'تاريخ الشراء',
        'received_date' => 'تاريخ الاستلام',
        'delivery_date' => 'تاريخ التسليم',
        'device_password' => 'كلمة سر الجهاز',
        'problem_description' => 'وصف المشكلة',
        'barcode' => 'الباركود',
        'unit_price' => 'سعر الوحدة',
    ];

    public function modelLabel(string $modelType): string
    {
        return $this->modelLabels[$modelType] ?? class_basename($modelType);
    }

    public function actionLabel(string $action): string
    {
        return $this->actionLabels[$action] ?? $action;
    }

    public function fieldLabel(string $field): string
    {
        return $this->fieldLabels[$field] ?? str_replace('_', ' ', $field);
    }

    /**
     * الحقول اللي مالها داعي نعرضها بالتفاصيل (تقنية بحتة أو مكررة بمكان تاني بالصف).
     */
    protected array $hiddenFields = [
        'id', 'created_at', 'updated_at', 'deleted_at', 'password',
        'remember_token', 'email_verified_at',
    ];

    public function formatValue($value): string
    {
        if (is_null($value)) {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'نعم' : 'لا';
        }
        if (is_numeric($value) && str_contains((string) $value, '.')) {
            return number_format((float) $value, 2);
        }
        $str = (string) $value;
        if (mb_strlen($str) > 80) {
            return mb_substr($str, 0, 80) . '…';
        }
        return $str;
    }

    /**
     * @return array<int, array{field: string, old: string, new: string}>
     */
    public function formatChangesForUpdate(?array $changes): array
    {
        if (!$changes) {
            return [];
        }

        $rows = [];
        foreach ($changes as $field => $diff) {
            if (in_array($field, $this->hiddenFields, true)) {
                continue;
            }
            if (!is_array($diff) || !array_key_exists('old', $diff) || !array_key_exists('new', $diff)) {
                continue;
            }
            $rows[] = [
                'field' => $this->fieldLabel($field),
                'old' => $this->formatValue($diff['old']),
                'new' => $this->formatValue($diff['new']),
            ];
        }
        return $rows;
    }

    /**
     * @return array<int, array{field: string, value: string}>
     */
    public function formatAttributesForCreate(?array $attributes): array
    {
        if (!$attributes) {
            return [];
        }

        $rows = [];
        foreach ($attributes as $field => $value) {
            if (in_array($field, $this->hiddenFields, true)) {
                continue;
            }
            if (is_array($value) || is_null($value) || $value === '') {
                continue;
            }
            $rows[] = [
                'field' => $this->fieldLabel($field),
                'value' => $this->formatValue($value),
            ];
        }
        return $rows;
    }
}
