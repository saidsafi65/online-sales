<?php

namespace App\Models;

use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasBranchScope;

    protected $fillable = [
        'invoice_id', 'cash_amount', 'bank_amount', 'payment_date', 'received_by', 'notes',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->cash_amount + (float) $this->bank_amount;
    }
}
