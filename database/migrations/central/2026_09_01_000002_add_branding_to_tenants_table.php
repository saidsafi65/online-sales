<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('is_active');
            $table->string('brand_primary_color', 7)->nullable()->after('logo_path');
            $table->string('brand_accent_color', 7)->nullable()->after('brand_primary_color');
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'brand_primary_color', 'brand_accent_color']);
        });
    }
};
