<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_laptops', function (Blueprint $table) {
            $table->foreignId('purchase_id')->nullable()->after('id')
                ->constrained('purchases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sale_laptops', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropColumn('purchase_id');
        });
    }
};
