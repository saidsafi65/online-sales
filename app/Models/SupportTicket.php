<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'tenant_id', 'local_user_id', 'submitter_name',
        'type', 'subject', 'message',
        'status', 'admin_reply', 'admin_reply_at',
    ];

    protected $casts = [
        'admin_reply_at' => 'datetime',
    ];

    public const TYPES = [
        'bug' => 'خطأ برمجي',
        'problem' => 'مشكلة بالنظام',
        'change_request' => 'طلب تعديل',
        'other' => 'أخرى',
    ];

    public const STATUSES = [
        'open' => 'مفتوحة',
        'in_progress' => 'قيد المعالجة',
        'resolved' => 'تم الحل',
        'closed' => 'مغلقة',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
