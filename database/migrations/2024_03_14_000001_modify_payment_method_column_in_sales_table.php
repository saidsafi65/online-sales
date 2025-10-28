<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'payment_method')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('payment_method', 20)->change();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'payment_method')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('payment_method', 10)->change();
            });
        }
    }
};
