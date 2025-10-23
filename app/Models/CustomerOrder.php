<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class CustomerOrder extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'customer_name',
        'phone_number',
        'device_type',
        'specifications',
        'approximate_price',
        'status',
        'notes',
    ];

    protected $casts = [
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
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
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
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}
