<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class GoogleSearchSettingsController extends Controller
{
    public function edit()
    {
        $tenant = app('currentTenant');

        return view('admin.google-search-settings', compact('tenant'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'google_search_api_key' => 'nullable|string|max:500',
            'google_search_cx' => 'nullable|string|max:255',
            'gemini_api_key' => 'nullable|string|max:500',
        ]);

        /** @var Tenant $tenant */
        $tenant = app('currentTenant');

        $tenant->google_search_cx = $validated['google_search_cx'] ?? null;

        // المفاتيح ما بنعرضها أبداً بالفورم (أمان) — لو حقل انترك فاضي منسيبه زي ما هو محفوظ حالياً.
        if (! empty($validated['google_search_api_key'])) {
            $tenant->google_search_api_key = $validated['google_search_api_key'];
        }
        if (! empty($validated['gemini_api_key'])) {
            $tenant->gemini_api_key = $validated['gemini_api_key'];
        }

        $tenant->save();

        return back()->with('success', 'تم تحديث إعدادات خدمات Google بنجاح');
    }
}
