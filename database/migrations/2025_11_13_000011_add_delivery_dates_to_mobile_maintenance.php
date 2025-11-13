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
        Schema::table('mobile_maintenance', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('bank_amount');
            $table->date('receipt_date')->nullable()->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_maintenance', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'receipt_date']);
        });
    }
};
