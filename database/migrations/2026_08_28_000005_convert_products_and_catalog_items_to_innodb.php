<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE products ENGINE=InnoDB');
        DB::statement('ALTER TABLE catalog_items ENGINE=InnoDB');
    }

    public function down(): void
    {
        // ما في داعي رجوع لـ MyISAM، InnoDB أفضل وأأمن دايماً
    }
};