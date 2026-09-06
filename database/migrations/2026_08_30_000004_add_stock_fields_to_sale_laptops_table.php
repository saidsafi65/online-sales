<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_laptops', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_laptops', 'quantity')) {
                $table->unsignedInteger('quantity')->default(0)->after('discount');
            }
            if (!Schema::hasColumn('sale_laptops', 'catalog_item_id')) {
                $table->foreignId('catalog_item_id')->nullable()->after('quantity')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('sale_laptops', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('catalog_item_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_laptops', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'catalog_item_id', 'branch_id']);
        });
    }
};