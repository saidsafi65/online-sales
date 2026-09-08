<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasBranchScope;

class Purchase extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $table = 'purchases';

    protected $fillable = [
        'item',
        'type',
        'quantity',
        'payment_method',
        'amount_cash',
        'amount_bank',
        'purchase_date',
        'supplier_name',
        'phone',
        'id_image',
        'is_returned',
        'issue',
        'return_date',
        'notes',
        'catalog_item_id',
        'catalog_quantity_applied',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount_cash' => 'decimal:2',
        'amount_bank' => 'decimal:2',
        'purchase_date' => 'datetime',
        'return_date' => 'datetime',
        'is_returned' => 'boolean',
        'catalog_item_id' => 'integer',
        'catalog_quantity_applied' => 'integer',
    ];

    public function catalogItem()
    {
        return $this->belongsTo(CatalogItem::class);
    }
}
