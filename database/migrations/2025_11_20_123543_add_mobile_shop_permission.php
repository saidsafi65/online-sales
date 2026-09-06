<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // التحقق من وجود الأعمدة قبل إضافتها
        if (!Schema::hasColumn('users', 'can_view_mobile_shop')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('can_view_mobile_shop')->default(false)->after('can_view_products');
            });
        }

        if (!Schema::hasColumn('users', 'is_mobile_shop_only')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_mobile_shop_only')->default(false)->after('can_view_mobile_shop');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_view_mobile_shop', 'is_mobile_shop_only']);
        });
    }
};
