<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class MobileInventory extends Model
{
    use HasBranchScope;

    protected $table = 'mobile_inventory';

    protected $fillable = [
        'product_name',
        'model_type',
        'quantity',
        'wholesale_price',
        'selling_price',
        'branch_id'
    ];

    protected $casts = [
        'wholesale_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
