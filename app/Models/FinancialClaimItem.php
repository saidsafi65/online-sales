<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialClaimItem extends Model
{
    protected $fillable = [
        'financial_claim_id', 'item_number', 'description', 'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function claim()
    {
        return $this->belongsTo(FinancialClaim::class, 'financial_claim_id');
    }
}
