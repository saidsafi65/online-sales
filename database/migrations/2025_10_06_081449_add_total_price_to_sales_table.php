<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحقق من وجود الجدول أولاً
        if (!Schema::hasTable('sales')) {
            return;
        }

        // أضف العمود فقط إن لم يكن موجوداً
        if (!Schema::hasColumn('sales', 'total_price')) {
            Schema::table('sales', function (Blueprint $table) {
                // Do not force column position; some environments may not have 'quantity' column yet
                $table->decimal('total_price', 10, 2)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         if (!Schema::hasTable('sales')) {
            return;
        }

        // احذف العمود فقط إن كان موجوداً
        if (Schema::hasColumn('sales', 'total_price')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('total_price');
            });
        }
    }
};
