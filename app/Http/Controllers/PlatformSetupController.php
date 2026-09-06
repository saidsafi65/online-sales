<?php

namespace App\Http\Controllers;

use App\Models\PlatformAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * One-time bootstrap for the very first Super Admin account. There is no
 * SSH/artisan access on production, so this is a plain web form instead of
 * a console command — but it self-locks the moment one platform_admins row
 * exists, so it can never be used to add a second/rogue account later.
 */
class PlatformSetupController extends Controller
{
    public function show()
    {
        if (PlatformAdmin::on('central')->exists()) {
            return redirect()->route('system-admin.login')->with('error', 'تم إعداد حساب مدير النظام مسبقاً.');
        }

        return view('platform.setup');
    }

    public function store(Request $request)
    {
        if (PlatformAdmin::on('central')->exists()) {
            return redirect()->route('system-admin.login')->with('error', 'تم إعداد حساب مدير النظام مسبقاً.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => ['required', Password::min(8)],
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $admin = PlatformAdmin::on('central')->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('platform')->login($admin);
        $request->session()->regenerate();

        return redirect()->route('system-admin.dashboard')->with('success', 'تم إنشاء حساب مدير النظام بنجاح');
    }
}
