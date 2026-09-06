<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Registers the current store as tenant #1, using the same DB
     * credentials already configured in .env (DB_*). This makes the
     * matched-tenant path and the no-match fallback path resolve to
     * the exact same database, so registering it changes nothing.
     */
    public function run(): void
    {
        $credentials = [
            'db_host' => env('DB_HOST', '127.0.0.1'),
            'db_port' => env('DB_PORT', '3306'),
            'db_database' => env('DB_DATABASE', 'laravel'),
            'db_username' => env('DB_USERNAME', 'root'),
            'db_password' => env('DB_PASSWORD', ''),
            'is_active' => true,
        ];

        foreach (['online-sale.site', 'localhost', '127.0.0.1'] as $domain) {
            Tenant::on('central')->updateOrCreate(
                ['domain' => $domain],
                array_merge($credentials, ['name' => 'Online Sale'])
            );
        }
    }
}
