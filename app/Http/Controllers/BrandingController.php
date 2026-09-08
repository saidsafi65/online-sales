<?php

namespace App\Http\Controllers;

use App\Services\IconGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandingController extends Controller
{
    public function edit()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $tenant = app('currentTenant');
        $iconExists = Storage::disk('public')->exists('icons/'.$tenant->id.'/icon-192.png');

        return view('admin.branding', [
            'tenant' => $tenant,
            'iconExists' => $iconExists,
            'iconUrl' => $iconExists ? asset('storage/icons/'.$tenant->id.'/icon-192.png') : null,
        ]);
    }

    public function update(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'brand_primary_color' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'brand_accent_color' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:1024',
            'stamp' => 'nullable|image|mimes:png|max:1024',
            'signature' => 'nullable|image|mimes:png|max:1024',
            'contact_phone' => 'nullable|string|max:30|regex:/^[0-9+\-\s]*$/',
            'contact_whatsapp' => 'nullable|string|max:30|regex:/^[0-9+\-\s]*$/',
        ], [
            'brand_primary_color.regex' => 'صيغة اللون غير صحيحة',
            'brand_accent_color.regex' => 'صيغة اللون غير صحيحة',
            'stamp.mimes' => 'الختم لازم يكون صورة PNG (مفرغة الخلفية)',
            'signature.mimes' => 'التوقيع لازم يكون صورة PNG (مفرغة الخلفية)',
            'contact_phone.regex' => 'رقم الهاتف غير صحيح',
            'contact_whatsapp.regex' => 'رقم الواتساب غير صحيح',
        ]);

        $tenant = app('currentTenant');

        if ($request->hasFile('logo')) {
            $logoName = Str::random(20).'.'.$request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->storeAs('logos', $logoName, 'public');
            $validated['logo_path'] = 'logos/'.$logoName;
        }

        if ($request->hasFile('stamp')) {
            $stampName = Str::random(20).'.png';
            $request->file('stamp')->storeAs('stamps', $stampName, 'public');
            $validated['stamp_path'] = 'stamps/'.$stampName;
        }

        if ($request->hasFile('signature')) {
            $signatureName = Str::random(20).'.png';
            $request->file('signature')->storeAs('signatures', $signatureName, 'public');
            $validated['signature_path'] = 'signatures/'.$signatureName;
        }

        $tenant->update($validated);

        return back()->with('success', 'تم تحديث هوية المعرض بنجاح');
    }

    public function updateIcon(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'icon_source' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $tenant = app('currentTenant');

        $sourcePath = 'icons/'.$tenant->id.'/source.png';
        $request->file('icon_source')->storeAs('icons/'.$tenant->id, 'source.png', 'public');

        $generated = IconGenerator::generateSet($sourcePath, $tenant);

        if (! $generated) {
            return back()->with('error', 'تعذر معالجة الصورة، جرّب صورة PNG أو JPG أخرى');
        }

        return back()->with('success', 'تم حفظ أيقونة التطبيق بنجاح');
    }
}
