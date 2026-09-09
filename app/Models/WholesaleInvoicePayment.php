<?php

namespace App\Models;

use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;

class WholesaleInvoicePayment extends Model
{
    use HasBranchScope;

    protected $fillable = [
        'wholesale_invoice_id', 'cash_amount', 'bank_amount', 'payment_date', 'received_by', 'notes',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function wholesaleInvoice()
    {
        return $this->belongsTo(WholesaleInvoice::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->cash_amount + (float) $this->bank_amount;
    }
}
