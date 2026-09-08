<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ما كان في أي طريقة نعرف بعدين هل مشترى معيّن زاد كمية بالكتالوج وقت إنشائه (مسار
 * "شراء + كتالوج") ولا لأ (مسار "شراء" العادي) — فكان تعديل/إرجاع/حذف المشترى إما
 * يتجاهل الكتالوج بالكامل أو ينقص من صف كتالوج غلط (نفس الاسم بس مالوش علاقة).
 * هالعمودين بيربطوا كل مشترى بصف الكتالوج اللي أثّر فيه فعلياً (إن وجد) وبقديش
 * بالضبط، حتى نقدر نعكس بدقة بدل ما نخمّن.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('catalog_item_id')->nullable()->after('type')
                ->constrained('catalog_items')->nullOnDelete();
            $table->integer('catalog_quantity_applied')->default(0)->after('catalog_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('catalog_item_id');
            $table->dropColumn('catalog_quantity_applied');
        });
    }
};
