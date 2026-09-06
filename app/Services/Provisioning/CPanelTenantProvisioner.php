<?php

namespace App\Services\Provisioning;

use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Provisions a new tenant database on Bluehost via cPanel's UAPI, so the
 * store owner never has to touch phpMyAdmin/cPanel manually. Requires a
 * cPanel API token (Security > Manage API Tokens in cPanel) stored in
 * CPANEL_API_TOKEN. cPanel auto-prefixes any db/user name it creates with
 * the account's cPanel username (e.g. "ximnlmmy_"), so the returned
 * db_database/db_username already include that prefix.
 */
class CPanelTenantProvisioner implements TenantProvisioner
{
    public function provision(string $storeName, ?string $desiredSlug = null): array
    {
        $host = config('services.cpanel.host');
        $username = config('services.cpanel.username');
        $token = config('services.cpanel.token');

        if (! $host || ! $username || ! $token) {
            throw new RuntimeException('إعدادات cPanel API غير مكتملة (CPANEL_HOST / CPANEL_USERNAME / CPANEL_API_TOKEN).');
        }

        $slug = SlugGenerator::resolve($storeName, $desiredSlug);
        $password = Str::password(20);

        $this->call($host, $username, $token, 'Mysql/create_database', ['name' => $slug]);
        $this->call($host, $username, $token, 'Mysql/create_user', ['name' => $slug, 'password' => $password]);
        $this->call($host, $username, $token, 'Mysql/set_privileges_on_database', [
            'user' => "{$username}_{$slug}",
            'database' => "{$username}_{$slug}",
            'privileges' => 'ALL PRIVILEGES',
        ]);

        return [
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_database' => "{$username}_{$slug}",
            'db_username' => "{$username}_{$slug}",
            'db_password' => $password,
        ];
    }

    public function deprovision(Tenant $tenant): void
    {
        $host = config('services.cpanel.host');
        $username = config('services.cpanel.username');
        $token = config('services.cpanel.token');

        if (! $host || ! $username || ! $token) {
            throw new RuntimeException('إعدادات cPanel API غير مكتملة (CPANEL_HOST / CPANEL_USERNAME / CPANEL_API_TOKEN).');
        }

        // cPanel's delete endpoints take the bare name (no account prefix) — the same shape create_database/create_user were given.
        $slug = Str::startsWith($tenant->db_database, "{$username}_")
            ? Str::after($tenant->db_database, "{$username}_")
            : $tenant->db_database;

        $this->call($host, $username, $token, 'Mysql/delete_database', ['name' => $slug]);
        $this->call($host, $username, $token, 'Mysql/delete_user', ['name' => $slug]);
    }

    private function call(string $host, string $username, string $token, string $endpoint, array $params): void
    {
        $response = Http::withHeaders([
            'Authorization' => "cpanel {$username}:{$token}",
        ])->timeout(15)->get("https://{$host}:2083/execute/{$endpoint}", $params);

        $body = $response->json();

        if (! $response->successful() || empty($body['status'])) {
            $errors = $body['errors'] ?? [$response->body()];
            throw new RuntimeException("فشل التواصل مع cPanel ({$endpoint}): ".implode(', ', (array) $errors));
        }
    }
}
