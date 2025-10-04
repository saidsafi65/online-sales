<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obligation extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_type',
        'payment_type',
        'cash_amount',
        'bank_amount',
        'date',
        'detail',
    ];
}
