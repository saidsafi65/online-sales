<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->boolean('jawwalpay_enabled')->default(false);
            $table->string('jawwalpay_merchant_id')->nullable();
            $table->text('jawwalpay_secret_key')->nullable();
            $table->string('jawwalpay_base_url')->nullable();

            $table->boolean('bankofpalestine_enabled')->default(false);
            $table->string('bankofpalestine_merchant_id')->nullable();
            $table->text('bankofpalestine_secret_key')->nullable();
            $table->string('bankofpalestine_base_url')->nullable();

            $table->boolean('palpay_enabled')->default(false);
            $table->string('palpay_merchant_id')->nullable();
            $table->text('palpay_secret_key')->nullable();
            $table->string('palpay_base_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'jawwalpay_enabled', 'jawwalpay_merchant_id', 'jawwalpay_secret_key', 'jawwalpay_base_url',
                'bankofpalestine_enabled', 'bankofpalestine_merchant_id', 'bankofpalestine_secret_key', 'bankofpalestine_base_url',
                'palpay_enabled', 'palpay_merchant_id', 'palpay_secret_key', 'palpay_base_url',
            ]);
        });
    }
};
