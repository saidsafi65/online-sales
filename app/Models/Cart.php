<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['customer_id', 'coupon_code'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(fn ($item) => $item->price * $item->quantity);
    }

    /**
     * الكوبون المطبّق حالياً، بس لو لسا صالح فعلاً على إجمالي السلة الحالي — كوبون منتهي
     * أو تجاوز حد الاستخدام أو الإجمالي نزل تحت الحد الأدنى (بعد حذف منتج مثلاً) ما بيرجع هون.
     */
    public function getAppliedCouponAttribute(): ?Coupon
    {
        if (! $this->coupon_code) {
            return null;
        }

        $coupon = Coupon::where('code', $this->coupon_code)->first();

        return ($coupon && $coupon->isValidFor($this->total)) ? $coupon : null;
    }

    public function getDiscountAttribute(): float
    {
        $coupon = $this->applied_coupon;

        return $coupon ? $coupon->calculateDiscount($this->total) : 0.0;
    }

    public function getGrandTotalAttribute(): float
    {
        return max(0, round($this->total - $this->discount, 2));
    }
}