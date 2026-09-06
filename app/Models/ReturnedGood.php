<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class ReturnedGood extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'supplier_name',
        'product_name',
        'reason',
        'issue_discovered_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_discovered_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get status label in Arabic
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'قيد الانتظار',
            'returned' => 'تم الإرجاع',
            'replaced' => 'تم الاستبدال',
            'refunded' => 'تم الاسترداد',
            default => 'غير محدد',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'returned' => 'info',
            'replaced' => 'success',
            'refunded' => 'primary',
            default => 'secondary',
        };
    }
}
