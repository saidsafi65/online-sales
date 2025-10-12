<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'invoice_date', 'invoice_number', 'notes', 'total_amount', 'discount_amount', 'afterDiscount_amount',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
