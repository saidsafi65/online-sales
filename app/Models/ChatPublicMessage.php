<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatPublicMessage extends Model
{
    protected $connection = 'central';

    protected $fillable = ['chat_member_id', 'body', 'image'];

    public function member()
    {
        return $this->belongsTo(ChatMember::class, 'chat_member_id')->withTrashed();
    }
}
