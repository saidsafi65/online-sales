<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class MobileMaintenance extends Model
{
    use HasBranchScope;

    protected $table = 'mobile_maintenance';

    protected $fillable = [
        'customer_name',
        'phone_number',
        'problem_description',
        'mobile_type',
        'payment_method',
        'cost',
        'cash_amount',
        'bank_amount',
        'delivery_date',
        'receipt_date',
        'branch_id'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'delivery_date' => 'date',
        'receipt_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
