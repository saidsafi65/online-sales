<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Points a throwaway named connection at a given tenant's database. Shared
 * by anything that needs to reach into one specific store's data from
 * outside it (Super Admin tools) — migrations, account management, etc.
 */
class TenantDatabase
{
    public static function connect(Tenant $tenant, string $connectionName = 'tenant_dynamic'): string
    {
        Config::set("database.connections.{$connectionName}", array_merge(
            config('database.connections.mysql'),
            [
                'host' => $tenant->db_host,
                'port' => $tenant->db_port,
                'database' => $tenant->db_database,
                'username' => $tenant->db_username,
                'password' => $tenant->db_password,
            ]
        ));
        DB::purge($connectionName);

        return $connectionName;
    }
}
