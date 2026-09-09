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
            $table->string('contact_email')->nullable()->after('contact_whatsapp');
            $table->string('contact_address')->nullable()->after('contact_email');
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'contact_address']);
        });
    }
};
