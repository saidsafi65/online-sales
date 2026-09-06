<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreNotification extends Model
{
    protected $fillable = [
        'type', 'branch_id', 'title', 'body', 'url',
        'reference_type', 'reference_id', 'read_at',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeVisibleTo($query, $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->whereNull('branch_id')->orWhere('branch_id', $user->branch_id);
        });
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}