<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mobile shop actions each also create a mirrored row in the main
 * sales/purchases/debts/repairs tables (so they show up in the shop-wide
 * ledger), but the two were only ever linked by fuzzy name/phone/date
 * matching — editing or deleting the mobile-shop side left the mirrored
 * row stale (or, for debts, `debtsDestroy` could delete every match,
 * wiping unrelated debts). Real FKs let update/delete follow the link
 * exactly instead of guessing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_sales', function (Blueprint $table) {
            $table->foreignId('linked_sale_id')->nullable()->after('id')->constrained('sales')->nullOnDelete();
        });

        Schema::table('mobile_debts', function (Blueprint $table) {
            $table->foreignId('linked_debt_id')->nullable()->after('id')->constrained('debts')->nullOnDelete();
        });

        Schema::table('mobile_expenses', function (Blueprint $table) {
            $table->foreignId('linked_purchase_id')->nullable()->after('id')->constrained('purchases')->nullOnDelete();
        });

        Schema::table('mobile_maintenance', function (Blueprint $table) {
            $table->foreignId('linked_repair_id')->nullable()->after('id')->constrained('repairs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mobile_sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('linked_sale_id');
        });
        Schema::table('mobile_debts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('linked_debt_id');
        });
        Schema::table('mobile_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('linked_purchase_id');
        });
        Schema::table('mobile_maintenance', function (Blueprint $table) {
            $table->dropConstrainedForeignId('linked_repair_id');
        });
    }
};
