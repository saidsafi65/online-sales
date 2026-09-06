<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_type', 'actor_id', 'actor_name', 'action',
        'model_type', 'model_id', 'model_label', 'changes', 'branch_id',
    ];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }
}