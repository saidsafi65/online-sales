<?php

namespace App\Models;

use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    use HasBranchScope;

    protected $fillable = [
        'debt_id', 'wholesale_invoice_payment_id', 'cash_amount', 'bank_amount',
        'payment_date', 'received_by', 'notes',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->cash_amount + (float) $this->bank_amount;
    }
}
