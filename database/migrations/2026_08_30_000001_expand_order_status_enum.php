<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'failed', 'cancelled')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status ENUM('pending', 'paid', 'failed', 'cancelled')
            NOT NULL DEFAULT 'pending'
        ");
    }
};