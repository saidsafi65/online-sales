<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class MobileSale extends Model
{
    use HasBranchScope;

    protected $table = 'mobile_sales';

    protected $fillable = [
        'product_name',
        'product_type',
        'quantity',
        'payment_method',
        'cost',
        'cash_amount',
        'bank_amount',
        'branch_id',
        'created_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
