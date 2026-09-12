<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class PaymentGatewaySettingsController extends Controller
{
    private const GATEWAYS = ['jawwalpay', 'bankofpalestine', 'palpay'];

    public function edit()
    {
        $tenant = app('currentTenant');

        return view('admin.payment-gateways', compact('tenant'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'jawwalpay_enabled' => 'nullable|boolean',
            'jawwalpay_merchant_id' => 'nullable|string|max:255',
            'jawwalpay_secret_key' => 'nullable|string|max:500',
            'jawwalpay_base_url' => 'nullable|url|max:255',

            'bankofpalestine_enabled' => 'nullable|boolean',
            'bankofpalestine_merchant_id' => 'nullable|string|max:255',
            'bankofpalestine_secret_key' => 'nullable|string|max:500',
            'bankofpalestine_base_url' => 'nullable|url|max:255',

            'palpay_enabled' => 'nullable|boolean',
            'palpay_merchant_id' => 'nullable|string|max:255',
            'palpay_secret_key' => 'nullable|string|max:500',
            'palpay_base_url' => 'nullable|url|max:255',
        ]);

        /** @var Tenant $tenant */
        $tenant = app('currentTenant');

        foreach (self::GATEWAYS as $gateway) {
            $tenant->{"{$gateway}_enabled"} = $request->boolean("{$gateway}_enabled");
            $tenant->{"{$gateway}_merchant_id"} = $validated["{$gateway}_merchant_id"] ?? null;
            $tenant->{"{$gateway}_base_url"} = $validated["{$gateway}_base_url"] ?? null;

            // مفتاح السر ما بنعرضه أبداً بالفورم (أمان) — لو الحقل انترك فاضي منسيبه
            // زي ما هو محفوظ حالياً، ولو انكتبت فيه قيمة جديدة منحدّثه.
            if (! empty($validated["{$gateway}_secret_key"])) {
                $tenant->{"{$gateway}_secret_key"} = $validated["{$gateway}_secret_key"];
            }
        }

        $tenant->save();

        return back()->with('success', 'تم تحديث إعدادات طرق الدفع بنجاح');
    }
}
