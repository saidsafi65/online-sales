<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * محادثة المجتمع بين المعارض (tenants)، مش بين حسابات الموظفين. قبل هالميغريشن كان
 * كل موظف بفتح المحادثة بياخد صف ChatMember مستقل (unique على tenant_id+local_user_id)،
 * فكان موظفين المعرض الواحد يظهروا كأطراف محادثة منفصلة لبعض. هون بندمج كل أعضاء نفس
 * المعرض بصف واحد (نرحّل رسائلهم بدل ما نخسرها)، وبنمنع تكرار هيك مستقبلاً.
 */
return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        $duplicateGroups = DB::connection('central')->table('chat_members')
            ->select('tenant_id', DB::raw('MIN(id) as primary_id'))
            ->groupBy('tenant_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $primaryId = $group->primary_id;

            $duplicateIds = DB::connection('central')->table('chat_members')
                ->where('tenant_id', $group->tenant_id)
                ->where('id', '!=', $primaryId)
                ->pluck('id');

            if ($duplicateIds->isEmpty()) {
                continue;
            }

            $allIds = $duplicateIds->concat([$primaryId]);

            // رسائل الغرفة العامة: نرحّلها للعضو الرئيسي بدل ما تنحذف
            DB::connection('central')->table('chat_public_messages')
                ->whereIn('chat_member_id', $duplicateIds)
                ->update(['chat_member_id' => $primaryId]);

            // أي محادثة خاصة بين عضوين من نفس المعرض (نتيجة الخلل) ما إلها معنى — نحذفها
            // (رسائلها الخاصة بتترحّل تلقائياً عبر cascadeOnDelete)
            DB::connection('central')->table('chat_conversations')
                ->where(function ($q) use ($allIds) {
                    $q->whereIn('member_one_id', $allIds)->orWhereIn('member_two_id', $allIds);
                })
                ->get()
                ->each(function ($conv) use ($allIds) {
                    if ($allIds->contains($conv->member_one_id) && $allIds->contains($conv->member_two_id)) {
                        DB::connection('central')->table('chat_conversations')->where('id', $conv->id)->delete();
                    }
                });

            // أي محادثات متبقية (فعلياً مع معارض تانية) نرحّلها للعضو الرئيسي
            DB::connection('central')->table('chat_conversations')
                ->whereIn('member_one_id', $duplicateIds)->update(['member_one_id' => $primaryId]);
            DB::connection('central')->table('chat_conversations')
                ->whereIn('member_two_id', $duplicateIds)->update(['member_two_id' => $primaryId]);

            // مرسلو الرسائل الخاصة المتبقية
            DB::connection('central')->table('chat_private_messages')
                ->whereIn('sender_member_id', $duplicateIds)->update(['sender_member_id' => $primaryId]);

            // الأعضاء المكررين: رسائلهم كلها أُعيد تعيينها فوق، فحذفهم آمن
            DB::connection('central')->table('chat_members')->whereIn('id', $duplicateIds)->delete();
        }

        // اسم كل عضو = اسم المعرض نفسه (المحادثة بين معارض، مش أشخاص)
        DB::connection('central')->statement('
            UPDATE chat_members
            INNER JOIN tenants ON tenants.id = chat_members.tenant_id
            SET chat_members.name = tenants.name
        ');

        // MySQL بيرفض حذف الإندكس القديم لو ما في إندكس تاني يغطي tenant_id لصالح الـ
        // foreign key، فلازم نضيف الجديد قبل ما نحذف القديم (مو بنفس الأمر الواحد).
        Schema::connection('central')->table('chat_members', function (Blueprint $table) {
            $table->unique('tenant_id');
        });
        Schema::connection('central')->table('chat_members', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'local_user_id']);
        });
    }

    /**
     * الدمج أعلاه يفقد معلومة "مين بالضبط كان العضو الأصلي" — رجوع كامل غير ممكن.
     * هون بس نرجّع القيد القديم حتى يقدر يترحّل up() من جديد بدون تعارض.
     */
    public function down(): void
    {
        Schema::connection('central')->table('chat_members', function (Blueprint $table) {
            $table->unique(['tenant_id', 'local_user_id']);
        });
        Schema::connection('central')->table('chat_members', function (Blueprint $table) {
            $table->dropUnique(['tenant_id']);
        });
    }
};
