<?php

namespace App\Models;

use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;

class FinancialClaim extends Model
{
    use HasBranchScope;

    protected $fillable = [
        'claim_reference', 'claim_date', 'tor_number', 'currency', 'language',
        'organization_name', 'attention_name', 'position', 'total_amount', 'notes',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(FinancialClaimItem::class);
    }
}
