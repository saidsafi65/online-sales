<?php

namespace App\Models;

use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceQuote extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $fillable = [
        'quote_number', 'quote_date', 'valid_until', 'language', 'currency',
        'client_name', 'client_phone', 'client_email', 'notes',
        'total_amount', 'discount_amount', 'afterDiscount_amount',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PriceQuoteItem::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until !== null && $this->valid_until->isPast();
    }
}
