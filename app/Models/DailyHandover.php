<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class DailyHandover extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'handover_date',
        'handover_time',
        'cash',
        'bank',
        'reason',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'handover_date' => 'date',
        'cash' => 'decimal:2',
        'bank' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get formatted cash amount
     */
    public function getFormattedCashAttribute()
    {
        return number_format($this->cash, 2).' شيكل';
    }

    /**
     * Get formatted bank amount
     */
    public function getFormattedBankAttribute()
    {
        return number_format($this->bank, 2).' شيكل';
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('handover_date', [$startDate, $endDate]);
    }

    /**
     * Scope for specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('handover_date', $date);
    }
}
