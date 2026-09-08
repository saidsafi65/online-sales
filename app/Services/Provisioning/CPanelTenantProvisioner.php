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

        // cPanel UAPI بيرفض create_database/create_user لو الاسم ما بيبدأ ببادئة الحساب
        // (مثلاً "ximnlmmy_") — لازم نبعت الاسم كامل مع البادئة من البداية.
        $this->call($host, $username, $token, 'Mysql/create_database', ['name' => "{$username}_{$slug}"]);
        $this->call($host, $username, $token, 'Mysql/create_user', ['name' => "{$username}_{$slug}", 'password' => $password]);
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

        // مثل create_database/create_user، delete_database/delete_user كمان بتاخد الاسم
        // كامل مع بادئة الحساب (زي ما هو مخزّن أصلاً بـ db_database/db_username)، مش الاسم المجرّد.
        $this->call($host, $username, $token, 'Mysql/delete_database', ['name' => $tenant->db_database]);
        $this->call($host, $username, $token, 'Mysql/delete_user', ['name' => $tenant->db_username]);
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
