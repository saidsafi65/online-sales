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

    // حساب المبلغ الإجمالي
    public function getTotalAmountAttribute()
    {
        return $this->cash_amount + $this->bank_amount;
    }
}
