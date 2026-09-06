<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'transaction_id', 'status', 'amount', 'raw_response'];

    protected function casts(): array
    {
        return ['raw_response' => 'array'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}