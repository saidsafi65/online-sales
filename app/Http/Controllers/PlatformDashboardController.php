<?php

namespace App\Http\Controllers;

use App\Models\Tenant;

class PlatformDashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::on('central')->count();
        $activeTenants = Tenant::on('central')->where('is_active', true)->count();

        return view('system-admin.dashboard', [
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'inactiveTenants' => $totalTenants - $activeTenants,
        ]);
    }
}
