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
        // Add cash_amount and bank_amount to mobile_maintenance
        Schema::table('mobile_maintenance', function (Blueprint $table) {
            $table->decimal('cash_amount', 10, 2)->default(0)->after('payment_method');
            $table->decimal('bank_amount', 10, 2)->default(0)->after('cash_amount');
        });

        // Add cash_amount and bank_amount to mobile_sales
        Schema::table('mobile_sales', function (Blueprint $table) {
            $table->decimal('cash_amount', 10, 2)->default(0)->after('payment_method');
            $table->decimal('bank_amount', 10, 2)->default(0)->after('cash_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_maintenance', function (Blueprint $table) {
            $table->dropColumn(['cash_amount', 'bank_amount']);
        });

        Schema::table('mobile_sales', function (Blueprint $table) {
            $table->dropColumn(['cash_amount', 'bank_amount']);
        });
    }
};
