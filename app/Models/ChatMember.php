<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMember extends Model
{
    use SoftDeletes;

    protected $connection = 'central';

    protected $fillable = ['tenant_id', 'local_user_id', 'name', 'last_seen_public_at', 'last_read_public_message_id'];

    protected $casts = [
        'last_seen_public_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
