<?php

namespace App\Services\Provisioning;

use Illuminate\Support\Str;

/**
 * Resolves the ASCII-safe suffix used for a new tenant's database/user name
 * (before any account-prefix like "ximnlmmy_" gets added on top): a manually
 * typed name if given, otherwise derived from the store's name, otherwise a
 * random fallback for names that don't reduce to anything usable in ASCII
 * (e.g. purely Arabic names).
 */
class SlugGenerator
{
    public static function resolve(string $storeName, ?string $desiredSlug = null): string
    {
        if ($desiredSlug) {
            $clean = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $desiredSlug));
            $clean = ltrim($clean, '0123456789_');

            if ($clean !== '') {
                return substr($clean, 0, 32);
            }
        }

        $fromName = Str::slug($storeName, '_');
        if (strlen($fromName) >= 3) {
            return substr($fromName, 0, 32);
        }

        return 't_'.Str::lower(Str::random(8));
    }
}
