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
       $tables = [
            'sales',
            'repairs',
            'purchases',
            'catalog_items',
            'maintenance_deposits',
            'obligations',
            'invoices',
            'invoice_items',
            'laptops',
            'part_types',
            'parts',
            'laptop_parts',
            'part_compatibilities',
            'customer_orders',
            'daily_handovers',
            'returned_goods',
            'stores',
            'debts',
            'maintenance_parts',
            
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'sales',
            'repairs',
            'purchases',
            'catalog_items',
            'maintenance_deposits',
            'obligations',
            'invoices',
            'invoice_items',
            'laptops',
            'part_types',
            'parts',
            'laptop_parts',
            'part_compatibilities',
            'customer_orders',
            'daily_handovers',
            'returned_goods',
            'stores',
            'debts',
            'maintenance_parts',
            
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'branch_id')) {
                    $table->dropConstrainedForeignId('branch_id');
                }
            });
        }
    }
};
