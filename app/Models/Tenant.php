<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'name', 'domain', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password', 'is_active',
        'logo_path', 'brand_primary_color', 'brand_accent_color',
        'contact_phone', 'contact_whatsapp', 'contact_email', 'contact_address',
        'stamp_path', 'signature_path',
    ];

    protected $casts = [
        'db_password' => 'encrypted',
        'is_active' => 'boolean',
    ];

    /**
     * محادثة المجتمع بين المعارض (tenants) نفسها — كل معرض إله هوية محادثة واحدة
     * مشتركة بين كل موظفيه (شوف ChatController::currentMember). ننشئها فوراً لحظة
     * تسجيل المعرض حتى يظهر بقائمة "كل المعارض المسجلة" مباشرة، بدون ما ينتظر أول
     * موظف فيه يفتح صفحة المجتمع.
     */
    protected static function booted(): void
    {
        static::created(function (Tenant $tenant) {
            ChatMember::on('central')->firstOrCreate(
                ['tenant_id' => $tenant->id],
                ['name' => $tenant->name, 'local_user_id' => 0]
            );
        });
    }
}
