<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\SaleLaptop;
use App\Models\StoreNotification;
use App\Models\User;
use App\Notifications\StoreAlertPush;
use Illuminate\Support\Facades\Notification;
use Throwable;

class NotificationService
{
    protected static int $lowStockThreshold = 5;

    /**
     * Users who should receive a push for this alert: admins, plus anyone with
     * the matching section permission and matching (or store-wide) branch visibility —
     * mirrors User::canViewSection() and StoreNotification::scopeVisibleTo().
     */
    private static function pushEligibleUsers(StoreNotification $alert, string $permissionKey)
    {
        return User::where('status', 'active')
            ->get()
            ->filter(function (User $user) use ($alert, $permissionKey) {
                if (!$user->canViewSection($permissionKey)) {
                    return false;
                }
                if ($user->isAdmin()) {
                    return true;
                }
                return $alert->branch_id === null || $alert->branch_id === $user->branch_id;
            });
    }

    private static function pushAlert(StoreNotification $alert, string $permissionKey): void
    {
        // كل مستخدم بمحاولة إرسال منفصلة ومعزولة — Laravel بيوقف كامل الحلقة
        // بمجرد ما إشعار واحد يفشل (اشتراك ميت مثلاً)، فلو بعثنا للكل بنداء
        // واحد Notification::send($users, ...) ممكن مستخدم وحيد يمنع وصول
        // الإشعار لكل الباقين بعده.
        foreach (self::pushEligibleUsers($alert, $permissionKey) as $user) {
            try {
                Notification::send($user, new StoreAlertPush($alert));
            } catch (Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * تنبيه SMS لصاحب المعرض (رقم هاتف المعرض المسجّل بهوية المعرض) — بينبعت
     * بنفس شروط بث push بالضبط (تنبيه جديد أو تفاقم للأسوأ)، حتى ما يصير سبام.
     */
    private static function smsStockAlert(string $body): void
    {
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;
        $phone = $tenant->contact_phone ?? null;

        if (! $phone) {
            return;
        }

        try {
            $message = \App\Support\SmsTextBuilder::build(
                fn (string $b) => "تنبيه مخزون: {$b}",
                $body
            );
            app(\App\Services\SmsService::class)->send($phone, $message, 'stock_alert');
        } catch (Throwable $e) {
            report($e);
        }
    }

    public static function syncStock(Product|SaleLaptop $model): void
    {
        $isOut = $model->is_out_of_stock || $model->quantity <= 0;
        $isLow = !$isOut && $model->quantity < self::$lowStockThreshold;

        $referenceType = get_class($model);
        $urlBase = $model instanceof SaleLaptop ? '/laptops' : '/products';

        $existing = StoreNotification::where('reference_type', $referenceType)
            ->where('reference_id', $model->id)
            ->whereIn('type', ['low_stock', 'out_of_stock'])
            ->first();

        if (!$isOut && !$isLow) {
            if ($existing) {
                $existing->delete();
            }
            return;
        }

        $newType = $isOut ? 'out_of_stock' : 'low_stock';
        $rank = ['low_stock' => 1, 'out_of_stock' => 2];

        $title = $isOut ? 'نفذت الكمية' : 'كمية منخفضة';
        $body  = $model->name . ($isOut ? ' — نفذت من المخزون' : ' — تبقّى ' . $model->quantity . ' قطعة');

        if (!$existing) {
            $notification = StoreNotification::create([
                'type'           => $newType,
                'branch_id'      => $model->branch_id,
                'title'          => $title,
                'body'           => $body,
                'url'            => url($urlBase . '/' . $model->id . '/edit'),
                'reference_type' => $referenceType,
                'reference_id'   => $model->id,
                'read_at'        => null,
            ]);
            self::pushAlert($notification, 'products');
            self::smsStockAlert($body);
            return;
        }

        $becameWorse = $rank[$newType] > $rank[$existing->type];

        $existing->update([
            'type'    => $newType,
            'title'   => $title,
            'body'    => $body,
            'read_at' => $becameWorse ? null : $existing->read_at,
        ]);

        if ($becameWorse) {
            self::pushAlert($existing, 'products');
            self::smsStockAlert($body);
        }
    }

    public static function syncCatalogStock(CatalogItem $item): void
    {
        $quantity = (int) $item->quantity;
        $isOut = $quantity <= 0;
        $isLow = !$isOut && $quantity < self::$lowStockThreshold;

        $existing = StoreNotification::where('reference_type', CatalogItem::class)
            ->where('reference_id', $item->id)
            ->whereIn('type', ['low_stock', 'out_of_stock'])
            ->first();

        if (!$isOut && !$isLow) {
            if ($existing) {
                $existing->delete();
            }
            return;
        }

        $newType = $isOut ? 'out_of_stock' : 'low_stock';
        $rank = ['low_stock' => 1, 'out_of_stock' => 2];

        $label = $item->product . ($item->type ? ' — ' . $item->type : '');
        $title = $isOut ? 'نفذت الكمية بالكتالوج' : 'كمية منخفضة بالكتالوج';
        $body  = $label . ($isOut ? ' — نفذت من الكتالوج' : ' — تبقّى ' . $quantity);

        if (!$existing) {
            $notification = StoreNotification::create([
                'type'           => $newType,
                'branch_id'      => $item->branch_id,
                'title'          => $title,
                'body'           => $body,
                'url'            => url('/catalog/' . $item->id . '/edit'),
                'reference_type' => CatalogItem::class,
                'reference_id'   => $item->id,
                'read_at'        => null,
            ]);
            self::pushAlert($notification, 'products');
            self::smsStockAlert($body);
            return;
        }

        $becameWorse = $rank[$newType] > $rank[$existing->type];

        $existing->update([
            'type'    => $newType,
            'title'   => $title,
            'body'    => $body,
            'read_at' => $becameWorse ? null : $existing->read_at,
        ]);

        if ($becameWorse) {
            self::pushAlert($existing, 'products');
            self::smsStockAlert($body);
        }
    }

    /**
     * رابط سجل النشاطات المفلتر على عنصر معيّن — نستخدمه بدل رابط صفحة التعديل
     * بإشعارات الإضافة/التعديل/الحذف، عشان الضغط على الإشعار يوريك "شو صار"
     * (القيمة القديمة والجديدة) مش يودّيك مباشرة لفورم التعديل.
     */
    public static function activityLogUrl(string $modelClass, int $modelId): string
    {
        return url('/activity-log?model_type=' . urlencode($modelClass) . '&model_id=' . $modelId);
    }

    public static function notifyCrud(
        string $type,
        string $title,
        string $body,
        ?string $url,
        ?int $branchId,
        ?string $refType = null,
        ?int $refId = null
    ): void {
        StoreNotification::create([
            'type'           => $type,
            'branch_id'      => $branchId,
            'title'          => $title,
            'body'           => $body,
            'url'            => $url ? url($url) : null,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'read_at'        => null,
        ]);
    }

    public static function notifyNewOrder(Order $order): void
    {
        $notification = StoreNotification::create([
            'type'           => 'new_order',
            'branch_id'      => null,
            'title'          => 'طلب جديد',
            'body'           => $order->customer_name . ' — ' . number_format($order->total, 2) . ' ₪',
            'url'            => url('/online-orders/' . $order->id),
            'reference_type' => Order::class,
            'reference_id'   => $order->id,
            'read_at'        => null,
        ]);

        self::pushAlert($notification, 'online_orders');
    }
}