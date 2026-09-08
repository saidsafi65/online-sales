<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * توسيع قائمة الفروع الافتراضية (2025_10_19_121137_insert_default_branches) لتغطي
 * كل محافظات قطاع غزة، مش خانيونس والدير بس.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('branches')) {
            return;
        }

        // "الدير" اسم مختصر عامي لنفس مدينة "دير البلح" — نصحح التسمية بدل ما نكرر الفرع
        if (! DB::table('branches')->where('name', 'دير البلح')->exists()) {
            DB::table('branches')->where('name', 'الدير')->update(['name' => 'دير البلح']);
        }

        $names = [
            'دير البلح', 'غزة', 'شمال غزة', 'البريج', 'الزوايدة',
            'النصيرات', 'الشجاعية', 'الزيتون', 'المغازي', 'رفح',
        ];

        foreach ($names as $name) {
            if (! DB::table('branches')->where('name', $name)->exists()) {
                DB::table('branches')->insert(['name' => $name, 'location' => '']);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('branches')) {
            return;
        }

        DB::table('branches')->whereIn('name', [
            'غزة', 'شمال غزة', 'البريج', 'الزوايدة',
            'النصيرات', 'الشجاعية', 'الزيتون', 'المغازي', 'رفح',
        ])->delete();

        DB::table('branches')->where('name', 'دير البلح')->update(['name' => 'الدير']);
    }
};
