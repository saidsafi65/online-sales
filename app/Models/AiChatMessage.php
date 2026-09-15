<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'role', 'content'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiChatMessage $message) {
            $message->created_at = $message->created_at ?? now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
