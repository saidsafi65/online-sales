<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResolveTenantDatabase
{
    /**
     * Switches the 'mysql' connection to the requesting domain's tenant database.
     * Must run before anything touches a per-tenant model (User, Product, Order...).
     *
     * Fail-safe: if the domain isn't registered, or the central DB can't be reached,
     * this does nothing and the existing .env-based 'mysql' connection stands as-is.
     * A lookup failure here must never take the site down.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $tenant = Tenant::on('central')
                ->where('domain', $request->getHost())
                ->where('is_active', true)
                ->first();

            if ($tenant) {
                config(['database.connections.mysql' => array_merge(
                    config('database.connections.mysql'),
                    [
                        'host' => $tenant->db_host,
                        'port' => $tenant->db_port,
                        'database' => $tenant->db_database,
                        'username' => $tenant->db_username,
                        'password' => $tenant->db_password,
                    ]
                )]);

                DB::purge('mysql');

                app()->instance('currentTenant', $tenant);
            }
        } catch (Throwable $e) {
            Log::warning('Tenant resolution failed, falling back to default database connection.', [
                'host' => $request->getHost(),
                'error' => $e->getMessage(),
            ]);
        }

        return $next($request);
    }
}
