<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasBranchScope;

class Repair extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $table = 'repairs';

    protected $fillable = [
        'customer_name',
        'device_name',
        'model',
        'issue',
        'phone',
        'received_date',
        'cost_cash',
        'cost_bank',
        'payment_method',
        'delivery_date',
        'received_by',
        'is_returned',
        'return_reason',
        'return_date',
        'return_cost',
        'return_delivery_date',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'delivery_date' => 'datetime',
        'return_date' => 'datetime',
        'return_delivery_date' => 'datetime',
        'cost_cash' => 'decimal:2',
        'cost_bank' => 'decimal:2',
        'return_cost' => 'decimal:2',
        'is_returned' => 'boolean',
    ];
}
