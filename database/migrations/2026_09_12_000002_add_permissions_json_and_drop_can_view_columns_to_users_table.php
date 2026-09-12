<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LEGACY_FLAG_TO_MODULES = [
        'can_view_sales' => ['sales'],
        'can_view_repairs' => ['repairs'],
        'can_view_purchases' => ['purchases'],
        'can_view_catalog' => ['catalog'],
        'can_view_deposits' => ['deposits'],
        'can_view_reports' => ['reports'],
        'can_view_obligations' => ['obligations'],
        'can_view_invoices' => ['invoices', 'financial_claims', 'wholesale_invoices', 'price_quotes'],
        'can_view_compatibility' => ['compatibility'],
        'can_view_customer_orders' => ['customer_orders'],
        'can_view_daily_handovers' => ['daily_handovers'],
        'can_view_returned_goods' => ['returned_goods'],
        'can_view_store' => ['store'],
        'can_view_debts' => ['debts'],
        'can_view_backup' => ['backup'],
        'can_view_maintenance_parts' => ['maintenance_parts'],
        'can_view_products' => ['products'],
        'can_view_mobile_shop' => ['mobile_shop'],
        'can_view_online_orders' => ['online_orders'],
        'can_view_community' => ['community'],
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('role');
        });

        $flags = array_keys(self::LEGACY_FLAG_TO_MODULES);
        $rows = DB::table('users')->select(array_merge(['id'], $flags))->get();

        foreach ($rows as $row) {
            $granted = [];
            foreach (self::LEGACY_FLAG_TO_MODULES as $flag => $modules) {
                if (!$row->{$flag}) {
                    continue;
                }
                foreach ($modules as $module) {
                    foreach (\App\Support\PermissionRegistry::fullGrantFor($module) as $key) {
                        $granted[] = $key;
                    }
                }
            }

            if ($granted !== []) {
                DB::table('users')->where('id', $row->id)->update([
                    'permissions' => json_encode(array_values(array_unique($granted))),
                ]);
            }
        }

        Schema::table('users', function (Blueprint $table) use ($flags) {
            $table->dropColumn($flags);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (array_keys(self::LEGACY_FLAG_TO_MODULES) as $flag) {
                $table->boolean($flag)->default(false);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
