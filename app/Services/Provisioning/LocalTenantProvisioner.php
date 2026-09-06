<?php

namespace App\Services\Provisioning;

use App\Models\Tenant;
use PDO;
use PDOException;
use RuntimeException;

/**
 * Used for local development/testing (TENANT_PROVISIONER=local): creates the
 * database/user directly over SQL using a local admin MySQL account (XAMPP's
 * root user by default). Bluehost's shared MySQL user cannot run CREATE
 * DATABASE, so production uses CPanelTenantProvisioner instead.
 *
 * Prefixes the generated name with LOCAL_DB_PREFIX (default "ximnlmmy_", the
 * real Bluehost account prefix) purely so local database names look/group
 * the same way they will in production, where cPanel adds that prefix on
 * its own automatically.
 */
class LocalTenantProvisioner implements TenantProvisioner
{
    public function provision(string $storeName, ?string $desiredSlug = null): array
    {
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $adminUser = env('LOCAL_DB_ADMIN_USERNAME', 'root');
        $adminPassword = env('LOCAL_DB_ADMIN_PASSWORD', '');
        $prefix = env('LOCAL_DB_PREFIX', 'ximnlmmy_');

        $name = $prefix.SlugGenerator::resolve($storeName, $desiredSlug);
        $password = \Illuminate\Support\Str::password(20);

        try {
            $pdo = new PDO("mysql:host={$host};port={$port}", $adminUser, $adminPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('تعذّر الاتصال بسيرفر قاعدة البيانات المحلي: '.$e->getMessage());
        }

        if ($this->databaseExists($pdo, $name)) {
            throw new RuntimeException("اسم قاعدة البيانات \"{$name}\" مستخدم مسبقاً، جرّب اسم مختلف.");
        }

        try {
            $pdo->exec("CREATE DATABASE `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("CREATE USER '{$name}'@'{$host}' IDENTIFIED BY ".$pdo->quote($password));
            $pdo->exec("GRANT ALL PRIVILEGES ON `{$name}`.* TO '{$name}'@'{$host}'");
            $pdo->exec('FLUSH PRIVILEGES');
        } catch (PDOException $e) {
            throw new RuntimeException('تعذّر إنشاء قاعدة البيانات محلياً: '.$e->getMessage());
        }

        return [
            'db_host' => $host,
            'db_port' => $port,
            'db_database' => $name,
            'db_username' => $name,
            'db_password' => $password,
        ];
    }

    public function deprovision(Tenant $tenant): void
    {
        $adminUser = env('LOCAL_DB_ADMIN_USERNAME', 'root');
        $adminPassword = env('LOCAL_DB_ADMIN_PASSWORD', '');

        try {
            $pdo = new PDO(
                "mysql:host={$tenant->db_host};port={$tenant->db_port}",
                $adminUser,
                $adminPassword,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $pdo->exec("DROP DATABASE IF EXISTS `{$tenant->db_database}`");
            $pdo->exec("DROP USER IF EXISTS '{$tenant->db_username}'@'{$tenant->db_host}'");
        } catch (PDOException $e) {
            throw new RuntimeException('تعذّر حذف قاعدة البيانات محلياً: '.$e->getMessage());
        }
    }

    private function databaseExists(PDO $pdo, string $name): bool
    {
        $stmt = $pdo->prepare('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?');
        $stmt->execute([$name]);

        return (bool) $stmt->fetchColumn();
    }
}
