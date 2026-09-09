<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasBranchScope;

class Repair extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $table = 'repairs';

    protected $fillable = [
        'customer_name',
        'device_name',
        'model',
        'issue',
        'phone',
        'email',
        'received_date',
        'cost_cash',
        'cost_bank',
        'payment_method',
        'delivery_date',
        'received_by',
        'is_returned',
        'return_reason',
        'return_date',
        'return_cost',
        'return_delivery_date',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'delivery_date' => 'datetime',
        'return_date' => 'datetime',
        'return_delivery_date' => 'datetime',
        'cost_cash' => 'decimal:2',
        'cost_bank' => 'decimal:2',
        'return_cost' => 'decimal:2',
        'is_returned' => 'boolean',
    ];

    /**
     * نص رسالة الشكر الجاهزة (تكلفة الصيانة + الضمان) — نفس النص المستخدم بالإرسال
     * التلقائي عند إضافة الصيانة، ومستخدم كمان كبداية جاهزة لزر "إرسال رسالة" اليدوي.
     *
     * رسالة SMS عربية بتتحول تلقائياً لتشفير UCS-2 (لوجود حروف عربية)، وهاد التشفير
     * حده الأقصى لرسالة وحدة هو 70 حرف بس (مش 160 متل الإنجليزي) — لو تعدّت، بتنكسر
     * لرسالتين أو أكتر وتتضاعف التكلفة. فالنص هون مختصر قصداً، ومع قصّ تلقائي لاسم
     * الزبون لو لازم، حتى يضل دايماً رسالة وحدة بغض النظر عن طول الاسم أو المبلغ.
     */
    public function getCompletionSmsTextAttribute(): string
    {
        $totalCost = (float) $this->cost_cash + (float) $this->cost_bank;
        $costText = $totalCost == floor($totalCost)
            ? number_format($totalCost, 0)
            : number_format($totalCost, 2);

        $build = fn (string $name) => "شكراً {$name}، تمت صيانتك بـ{$costText}₪. ضمان 24 ساعة.";

        $name = (string) $this->customer_name;
        $message = $build($name);

        $overflow = mb_strlen($message) - 70;
        if ($overflow > 0) {
            $name = mb_substr($name, 0, max(1, mb_strlen($name) - $overflow));
            $message = $build($name);
        }

        return $message;
    }
}
