<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class MobileDebt extends Model
{
    use HasBranchScope;

    protected $table = 'mobile_debts';

    protected $fillable = [
        'customer_name',
        'phone_number',
        'type',
        'cash_amount',
        'bank_amount',
        'total',
        'debt_date',
        'payment_date',
        'branch_id'
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'debt_date' => 'date',
        'payment_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
