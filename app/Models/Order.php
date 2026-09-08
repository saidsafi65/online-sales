<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id', 'total', 'coupon_code', 'discount_amount', 'status', 'payment_method',
        'customer_name', 'customer_phone', 'shipping_address', 'shipping_city',
    ];

    /**
     * التسميات العربية لكل حالة — مصدر واحد مشترك بين لوحة تحكم الموظفين
     * (OnlineOrderController) وصفحات تتبع الطلب للعميل، حتى ما ينفصلوا عن بعض.
     */
    public const STATUS_LABELS = [
        'pending'    => 'قيد الانتظار',
        'paid'       => 'مدفوع',
        'processing' => 'قيد التجهيز',
        'shipped'    => 'تم الشحن',
        'delivered'  => 'تم التسليم',
        'failed'     => 'فشل الدفع',
        'cancelled'  => 'ملغي',
    ];

    /** الحالات "الطبيعية" المتتالية اللي بيبنى عليها شريط تتبع الطلب */
    public const STATUS_FLOW = ['pending', 'paid', 'processing', 'shipped', 'delivered'];

    /** حالات نهائية خارج المسار الطبيعي (توقف، مش خطوة تقدّم) */
    public const STATUS_TERMINAL_FAILURE = ['failed', 'cancelled'];

    /** الحالات اللي تعتبر "عملية شراء فعلية اكتملت" (تدفع أهلية المراجعة/التقييم) */
    public const STATUS_COUNTS_AS_PURCHASED = ['paid', 'processing', 'shipped', 'delivered'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}