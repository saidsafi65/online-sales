<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class InvoiceItem extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'invoice_id', 'item_number', 'description', 'quantity', 'unit_price', 'total_price',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
