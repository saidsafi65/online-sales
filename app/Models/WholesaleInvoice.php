<?php

namespace App\Models;

use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WholesaleInvoice extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $fillable = [
        'invoice_number', 'invoice_date', 'buyer_store_name', 'buyer_tax_number',
        'buyer_phone', 'buyer_address', 'payment_terms', 'due_date', 'cash_paid_now', 'debt_id',
        'total_amount', 'discount_amount', 'afterDiscount_amount', 'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public const PAYMENT_TERMS_LABELS = [
        'cash' => 'نقدي',
        'credit' => 'آجل',
        'mixed' => 'جزء نقدي وجزء آجل',
    ];

    public function items()
    {
        return $this->hasMany(WholesaleInvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(WholesaleInvoicePayment::class);
    }

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    /**
     * يحدّث سجل الدين المرتبط (لو موجود) ليطابق المتبقي الفعلي على الفاتورة —
     * بيتنادى بعد أي إضافة/حذف دفعة، حتى صفحة "الديون" تضل دقيقة بدون
     * ما نحتاج نلمس DebtController نفسه.
     */
    public function syncDebtStatus(): void
    {
        if (! $this->debt_id) {
            return;
        }

        $debt = $this->debt()->first();
        if (! $debt) {
            return;
        }

        $this->load('payments');
        $remaining = $this->remaining_amount;

        if ($remaining <= 0.01) {
            if (! $debt->payment_date) {
                $debt->payment_date = now();
                $debt->save();
            }
            return;
        }

        $debt->cash_amount = $remaining;
        $debt->bank_amount = 0;
        if ($debt->payment_date) {
            $debt->payment_date = null;
        }
        $debt->save();
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
