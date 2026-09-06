<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ManifestController extends Controller
{
    public function show(): JsonResponse
    {
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        $name = $tenant->name ?? 'Online Sale';
        $primaryColor = $tenant->brand_primary_color ?? '#dc2626';

        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => $primaryColor,
            'theme_color' => $primaryColor,
            'dir' => 'rtl',
            'lang' => 'ar',
            'icons' => $this->resolveIcons($tenant),
        ])->header('Content-Type', 'application/manifest+json');
    }

    private function resolveIcons(?Tenant $tenant): array
    {
        if ($tenant) {
            $dir = 'icons/'.$tenant->id;
            if (Storage::disk('public')->exists("$dir/icon-192.png")) {
                return [
                    ['src' => asset("storage/$dir/icon-192.png"), 'sizes' => '192x192', 'type' => 'image/png'],
                    ['src' => asset("storage/$dir/icon-512.png"), 'sizes' => '512x512', 'type' => 'image/png'],
                    ['src' => asset("storage/$dir/icon-512-maskable.png"), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
                ];
            }
        }

        return [
            ['src' => asset('icons/default/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
            ['src' => asset('icons/default/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
            ['src' => asset('icons/default/icon-512-maskable.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
        ];
    }
}
