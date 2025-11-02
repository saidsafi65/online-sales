<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_view_sales')->default(false);
            $table->boolean('can_view_repairs')->default(false);
            $table->boolean('can_view_purchases')->default(false);
            $table->boolean('can_view_catalog')->default(false);
            $table->boolean('can_view_deposits')->default(false);
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_view_obligations')->default(false);
            $table->boolean('can_view_invoices')->default(false);
            $table->boolean('can_view_compatibility')->default(false);
            $table->boolean('can_view_customer_orders')->default(false);
            $table->boolean('can_view_daily_handovers')->default(false);
            $table->boolean('can_view_returned_goods')->default(false);
            $table->boolean('can_view_store')->default(false);
            $table->boolean('can_view_debts')->default(false);
            $table->boolean('can_view_backup')->default(false);
            $table->boolean('can_view_maintenance_parts')->default(false);
            $table->boolean('can_view_products')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_view_sales',
                'can_view_repairs',
                'can_view_purchases',
                'can_view_catalog',
                'can_view_deposits',
                'can_view_reports',
                'can_view_obligations',
                'can_view_invoices',
                'can_view_compatibility',
                'can_view_customer_orders',
                'can_view_daily_handovers',
                'can_view_returned_goods',
                'can_view_store',
                'can_view_debts',
                'can_view_backup',
                'can_view_maintenance_parts',
                'can_view_products',
            ]);
        });
    }
};
