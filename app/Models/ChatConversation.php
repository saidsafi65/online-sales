<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $connection = 'central';

    protected $fillable = ['member_one_id', 'member_two_id'];

    public function memberOne()
    {
        return $this->belongsTo(ChatMember::class, 'member_one_id')->withTrashed();
    }

    public function memberTwo()
    {
        return $this->belongsTo(ChatMember::class, 'member_two_id')->withTrashed();
    }

    public function messages()
    {
        return $this->hasMany(ChatPrivateMessage::class, 'conversation_id');
    }

    public function otherMember(ChatMember $me): ChatMember
    {
        return $this->member_one_id === $me->id ? $this->memberTwo : $this->memberOne;
    }

    public function hasParticipant(ChatMember $member): bool
    {
        return $this->member_one_id === $member->id || $this->member_two_id === $member->id;
    }
}
