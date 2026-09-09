<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleInvoiceItem extends Model
{
    protected $fillable = [
        'wholesale_invoice_id', 'item_number', 'description', 'quantity', 'unit_price', 'total_price',
    ];

    public function wholesaleInvoice()
    {
        return $this->belongsTo(WholesaleInvoice::class);
    }
}
