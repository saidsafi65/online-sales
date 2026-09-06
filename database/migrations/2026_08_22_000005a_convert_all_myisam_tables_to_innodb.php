<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = DB::select("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND ENGINE = 'MyISAM'
              AND TABLE_TYPE = 'BASE TABLE'
        ");

        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            DB::statement("ALTER TABLE `{$tableName}` ENGINE=InnoDB");
        }
    }

    public function down(): void
    {
        // ما في داعي رجوع لـ MyISAM، InnoDB أفضل وأأمن دايماً
    }
};