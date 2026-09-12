<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'name', 'name_en', 'domain', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password', 'is_active',
        'logo_path', 'brand_primary_color', 'brand_accent_color',
        'contact_phone', 'contact_whatsapp', 'contact_email', 'contact_address', 'contact_address_en',
        'stamp_path', 'signature_path',
        'jawwalpay_enabled', 'jawwalpay_merchant_id', 'jawwalpay_secret_key', 'jawwalpay_base_url',
        'bankofpalestine_enabled', 'bankofpalestine_merchant_id', 'bankofpalestine_secret_key', 'bankofpalestine_base_url',
        'palpay_enabled', 'palpay_merchant_id', 'palpay_secret_key', 'palpay_base_url',
    ];

    protected $casts = [
        'db_password' => 'encrypted',
        'is_active' => 'boolean',
        'jawwalpay_enabled' => 'boolean',
        'jawwalpay_secret_key' => 'encrypted',
        'bankofpalestine_enabled' => 'boolean',
        'bankofpalestine_secret_key' => 'encrypted',
        'palpay_enabled' => 'boolean',
        'palpay_secret_key' => 'encrypted',
    ];

    public const PAYMENT_GATEWAYS = [
        'jawwalpay' => 'جوال باي',
        'bankofpalestine' => 'بنك فلسطين',
        'palpay' => 'بال باي',
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
