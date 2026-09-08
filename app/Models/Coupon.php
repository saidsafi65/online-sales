<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'max_uses', 'used_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * يتحقق إذا الكوبون صالح للاستخدام على إجمالي معيّن (نشط، ما انتهت صلاحيته، ما وصل
     * الحد الأقصى للاستخدام، والإجمالي وصل الحد الأدنى المطلوب لو كان محدد).
     */
    public function isValidFor(float $orderTotal): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }
        if ($this->min_order_amount !== null && $orderTotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        $discount = $this->type === 'percentage'
            ? $orderTotal * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $orderTotal), 2);
    }
}
