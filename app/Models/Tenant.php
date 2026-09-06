<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'name', 'domain', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password', 'is_active',
        'logo_path', 'brand_primary_color', 'brand_accent_color',
        'contact_phone', 'contact_whatsapp',
    ];

    protected $casts = [
        'db_password' => 'encrypted',
        'is_active' => 'boolean',
    ];
}
