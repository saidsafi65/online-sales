<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatPrivateMessage extends Model
{
    protected $connection = 'central';

    protected $fillable = ['conversation_id', 'sender_member_id', 'body', 'image', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(ChatMember::class, 'sender_member_id')->withTrashed();
    }
}
