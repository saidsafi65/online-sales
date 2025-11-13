<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class MobileExpense extends Model
{
    use HasBranchScope;

    protected $table = 'mobile_expenses';

    protected $fillable = [
        'category',
        'type',
        'quantity',
        'payment_method',
        'cash_amount',
        'bank_amount',
        'total',
        'expense_date',
        'supplier_name',
        'supplier_phone',
        'id_photo',
        'reference',
        'defect',
        'return_date',
        'notes',
        'branch_id'
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'expense_date' => 'date',
        'return_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
