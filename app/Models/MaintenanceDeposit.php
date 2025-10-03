<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceDeposit extends Model
{
    // 👇 هنا نضيف الحقول المسموح بإدخالها جماعيًا (Mass Assignment)
    protected $fillable = [
        'piece',
        'type',
        'reason',
        'taken_at',
        'returned_at',
    ];
}
