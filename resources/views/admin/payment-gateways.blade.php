@extends('layout.app')

@section('title', 'طرق الدفع')

@section('content')
<div class="container-fluid">
    <div style="max-width: 700px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem; color: #1e293b;">💳 طرق الدفع</h1>
        <p style="color:#64748b; margin-bottom:1.5rem;">حدد أي طرق الدفع تظهر للزبون بصفحة الدفع، وحط بيانات حساب كل بوابة (تقدر تغيّرها بأي وقت).</p>

        @if(session('success'))
            <div class="alert alert-success" style="border-radius: 12px;">✅ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 12px;">
                @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('payment-gateways.update') }}">
            @csrf

            @foreach (\App\Models\Tenant::PAYMENT_GATEWAYS as $key => $label)
                @php
                    $enabled = old("{$key}_enabled", $tenant->{"{$key}_enabled"});
                    $hasSecret = ! empty($tenant->{"{$key}_secret_key"});
                    $icons = ['jawwalpay' => '📱', 'bankofpalestine' => '🏦', 'palpay' => '👛'];
                @endphp
                <div class="card" style="border-radius: 18px; padding: 1.75rem; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 1.25rem;">
                        <h5 style="margin:0; font-weight:800; color:#1e293b;">{{ $icons[$key] }} {{ $label }}</h5>
                        <div class="form-check form-switch" style="direction: ltr;">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="{{ $key }}_enabled" name="{{ $key }}_enabled" value="1"
                                   style="width: 3em; height: 1.5em; cursor: pointer;"
                                   {{ $enabled ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; font-size:0.9rem;">Merchant ID</label>
                        <input type="text" name="{{ $key }}_merchant_id" class="form-control"
                               value="{{ old("{$key}_merchant_id", $tenant->{"{$key}_merchant_id"}) }}"
                               placeholder="رقم/معرّف التاجر عند الشركة"
                               style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.65rem 1rem;" dir="ltr">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; font-size:0.9rem;">Secret Key / API Key</label>
                        <input type="password" name="{{ $key }}_secret_key" class="form-control"
                               placeholder="{{ $hasSecret ? '•••••••• (محفوظ — اتركه فاضي لو ما بدك تغيّره)' : 'المفتاح السري' }}"
                               style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.65rem 1rem;" dir="ltr" autocomplete="new-password">
                        <small style="color:#94a3b8;">لأمانك، ما بنعرض المفتاح المحفوظ هون — اكتب قيمة جديدة بس لو بدك تغيّره.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label" style="font-weight: 600; font-size:0.9rem;">رابط API (Base URL)</label>
                        <input type="text" name="{{ $key }}_base_url" class="form-control"
                               value="{{ old("{$key}_base_url", $tenant->{"{$key}_base_url"}) }}"
                               placeholder="https://api.example.com/v1"
                               style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.65rem 1rem;" dir="ltr">
                    </div>
                </div>
            @endforeach

            <div class="alert alert-info" style="border-radius: 12px; font-size: 0.9rem;">
                💡 لو فعّلت بوابة قبل ما تحط بياناتها، بتضل شغالة بوضع تجريبي (يقدر الزبون يجرب الدفع بس ما بينبعث لشركة حقيقية) لحد ما تحط Merchant ID و Secret Key.
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.875rem; border-radius: 10px; font-weight: 600;">
                💾 حفظ الإعدادات
            </button>
        </form>
    </div>
</div>
@endsection
