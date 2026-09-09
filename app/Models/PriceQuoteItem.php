<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceQuoteItem extends Model
{
    protected $fillable = [
        'price_quote_id', 'item_number', 'description', 'quantity', 'unit_price', 'total_price',
    ];

    public function priceQuote()
    {
        return $this->belongsTo(PriceQuote::class);
    }
}
