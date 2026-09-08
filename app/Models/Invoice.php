<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class Invoice extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'customer_name', 'invoice_date', 'invoice_number', 'notes', 'total_amount', 'discount_amount', 'afterDiscount_amount',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments->sum(fn ($p) => (float) $p->cash_amount + (float) $p->bank_amount);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, round((float) $this->afterDiscount_amount - $this->paid_amount, 2));
    }

    /** open | partial | paid */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->paid_amount <= 0) {
            return 'open';
        }

        return $this->remaining_amount <= 0 ? 'paid' : 'partial';
    }

    public const PAYMENT_STATUS_LABELS = [
        'open' => 'غير مدفوعة',
        'partial' => 'مدفوعة جزئياً',
        'paid' => 'مدفوعة بالكامل',
    ];
}
