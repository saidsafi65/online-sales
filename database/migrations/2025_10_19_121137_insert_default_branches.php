<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       // تحقق من وجود الجدول قبل الإدراج
        if (!Schema::hasTable('branches')) {
            return;
        }

        $branches = [
            ['name' => 'خانيونس'],
            ['name' => 'الدير'],
        ];

        // استخدم insertOrIgnore لتجنب أخطاء التكرار
        foreach ($branches as $branch) {
            DB::table('branches')->insertOrIgnore($branch);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
               if (!Schema::hasTable('branches')) {
            return;
        }

        DB::table('branches')->whereIn('name', ['خانيونس', 'الدير'])->delete();
    
    }
};
