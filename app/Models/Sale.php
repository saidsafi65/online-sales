<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasBranchScope;

class Sale extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $table = 'sales';

    protected $fillable = [
        'product',
        'type',
        'quantity',
        'sale_date',
        'payment_method',
        'cash_amount',
        'app_amount',
        'is_returned',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'cash_amount' => 'decimal:2',
        'app_amount' => 'decimal:2',
        'quantity' => 'integer',
        'is_returned' => 'boolean',
    ];

    protected $dates = [
        'sale_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Accessors
    public function getTotalAmountAttribute()
    {
        return $this->cash_amount + $this->app_amount;
    }

    public function getFormattedSaleDateAttribute()
    {
        return $this->sale_date ? $this->sale_date->format('Y-m-d') : null;
    }

    // Scopes
    public function scopeNotReturned($query)
    {
        return $query->where('is_returned', false);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }
}
