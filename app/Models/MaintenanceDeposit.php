<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class MaintenanceDeposit extends Model
{
    use HasBranchScope;
    // 👇 هنا نضيف الحقول المسموح بإدخالها جماعيًا (Mass Assignment)
    protected $fillable = [
        'piece',
        'type',
        'reason',
        'taken_at',
        'returned_at',
    ];
}
