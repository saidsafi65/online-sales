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
        if (Schema::hasTable('mobile_maintenance') && !Schema::hasColumn('mobile_maintenance', 'delivery_date')) {
            Schema::table('mobile_maintenance', function (Blueprint $table) {
                $table->date('delivery_date')->nullable()->after('bank_amount');
                $table->date('receipt_date')->nullable()->after('delivery_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mobile_maintenance') && Schema::hasColumn('mobile_maintenance', 'delivery_date')) {
            Schema::table('mobile_maintenance', function (Blueprint $table) {
                $table->dropColumn(['delivery_date', 'receipt_date']);
            });
        }
    }
};

