<?php

namespace App\Notifications\Concerns;

use Illuminate\Support\Facades\Storage;

trait ResolvesTenantPushIcon
{
    private function resolveIcon(): string
    {
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        if ($tenant && Storage::disk('public')->exists("icons/{$tenant->id}/icon-192.png")) {
            return asset("storage/icons/{$tenant->id}/icon-192.png");
        }

        return asset('icons/default/icon-192.png');
    }
}
