<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class Debt extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'customer_name',
        'phone',
        'type',
        'cash_amount',
        'bank_amount',
        'reason',
        'debt_date',
        'payment_date',
    ];

    protected $casts = [
        'debt_date' => 'date',
        'payment_date' => 'date',
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
    ];

    // حساب المبلغ الإجمالي (الأصلي وقت إنشاء الدين)
    public function getTotalAmountAttribute()
    {
        return $this->cash_amount + $this->bank_amount;
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    /**
     * لو الدين جاي أصلاً من فاتورة بيع بالجملة (آجلة/مختلطة) — دفعاتها بتنسجل
     * من صفحة الفاتورة نفسها، مش من هون مباشرة، حتى يضل مصدر واحد للحقيقة.
     */
    public function wholesaleInvoice()
    {
        return $this->hasOne(WholesaleInvoice::class, 'debt_id');
    }

    public function getPaidAmountAttribute(): float
    {
        $fromPayments = (float) $this->payments->sum(fn ($p) => (float) $p->cash_amount + (float) $p->bank_amount);

        // ديون قديمة اتحطلها تاريخ سداد يدوياً (الطريقة القديمة قبل نظام الدفعات)
        // بدون أي دفعة مسجّلة فعلياً — لازم تضل تُحسب "مسددة بالكامل"، مش "غير مسدد".
        if ($this->payment_date && $fromPayments <= 0) {
            return (float) $this->total_amount;
        }

        return $fromPayments;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, round((float) $this->total_amount - $this->paid_amount, 2));
    }

    /** open | partial | paid */
    public function getPaymentStatusAttribute(): string
    {
        // القاعدة الأساسية: إله تاريخ سداد = مسدد، ماله تاريخ = لسا مو مسدد بالكامل —
        // بغض النظر عن المبلغ (بيغطي حالة دين بمبلغ صفر متعلّم "مسدد" يدوياً).
        if ($this->payment_date) {
            return 'paid';
        }

        if ($this->paid_amount <= 0) {
            return 'open';
        }

        return $this->remaining_amount <= 0 ? 'paid' : 'partial';
    }

    public const PAYMENT_STATUS_LABELS = [
        'open' => 'غير مسدد',
        'partial' => 'مسدد جزئياً',
        'paid' => 'مسدد بالكامل',
    ];

    /**
     * يحدّث تاريخ السداد تلقائياً حسب حالة الدفعات الفعلية — تنادى بعد أي
     * إضافة/حذف دفعة، حتى الحقل القديم payment_date يضل متوافق مع
     * حساب الديون الإجمالية بصفحة القائمة (اللي بيعتمد عليه أصلاً).
     */
    public function syncPaymentStatus(): void
    {
        $this->load('payments');

        if ($this->remaining_amount <= 0.01) {
            if (! $this->payment_date) {
                $this->payment_date = now();
                $this->save();
            }
            return;
        }

        if ($this->payment_date) {
            $this->payment_date = null;
            $this->save();
        }
    }
}
