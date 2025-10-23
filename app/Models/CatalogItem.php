<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    use HasFactory, HasBranchScope;

    protected $table = 'catalog_items';

    protected $fillable = [
        'product',
        'type',
        'quantity',
        'wholesale_price',
        'sale_price',
    ];
}
