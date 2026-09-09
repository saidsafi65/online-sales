<?php

namespace App\Models;

use App\Models\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasBranchScope;

    protected $fillable = [
        'phone', 'message', 'purpose', 'status', 'provider_response',
    ];

    public const STATUS_LABELS = [
        'mock' => 'وهمي (تجريبي)',
        'sent' => 'أُرسلت',
        'failed' => 'فشلت',
    ];
}
