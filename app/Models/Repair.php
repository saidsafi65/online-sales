<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasBranchScope;

class Repair extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $table = 'repairs';

    protected $fillable = [
        'customer_name',
        'device_name',
        'model',
        'issue',
        'phone',
        'email',
        'received_date',
        'cost_cash',
        'cost_bank',
        'payment_method',
        'delivery_date',
        'received_by',
        'is_returned',
        'return_reason',
        'return_date',
        'return_cost',
        'return_delivery_date',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'delivery_date' => 'datetime',
        'return_date' => 'datetime',
        'return_delivery_date' => 'datetime',
        'cost_cash' => 'decimal:2',
        'cost_bank' => 'decimal:2',
        'return_cost' => 'decimal:2',
        'is_returned' => 'boolean',
    ];

    /**
     * نص رسالة الشكر الجاهزة (تكلفة الصيانة + الضمان) — نفس النص المستخدم بالإرسال
     * التلقائي عند إضافة الصيانة، ومستخدم كمان كبداية جاهزة لزر "إرسال رسالة" اليدوي.
     */
    public function getCompletionSmsTextAttribute(): string
    {
        $storeName = app()->bound('currentTenant') ? app('currentTenant')->name : 'Online Sale';
        $totalCost = (float) $this->cost_cash + (float) $this->cost_bank;

        return "شكراً {$this->customer_name} لثقتك بـ{$storeName}. تمت صيانة {$this->device_name} بنجاح بتكلفة "
            . number_format($totalCost, 2) . ' شيكل. معك ضمان 24 ساعة على الصيانة من تاريخ الاستلام.';
    }
}
