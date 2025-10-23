<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class Store extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'product_name',
        'product_type',
        'quantity',
        'supplier_name',
        'wholesale_price',
        'payment_method',
        'cash_amount',
        'bank_amount',
        'date_added',
    ];
}
