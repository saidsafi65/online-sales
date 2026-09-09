@php
    $isAr = $claim->language === 'ar';
    $labels = $isAr ? [
        'title' => 'مطالبة مالية', 'subtitle' => 'طلب دفع',
        'claim_ref' => 'رقم المطالبة', 'date' => 'التاريخ', 'tor' => 'رقم ToR', 'currency' => 'العملة',
        'recipient' => 'بيانات المستلم', 'organization' => 'المؤسسة / الجهة', 'attention' => 'للعناية', 'position' => 'المنصب',
        'details' => 'تفاصيل المطالبة', 'no' => 'رقم', 'desc' => 'الوصف', 'amount' => 'المبلغ',
        'grand_total' => 'الإجمالي الكلي', 'terms' => 'الشروط والملاحظات', 'contact' => 'معلومات التواصل',
        'mobile' => 'الجوال', 'whatsapp' => 'واتساب', 'location' => 'الموقع',
        'signature' => 'التوقيع المعتمد', 'stamp' => 'ختم الشركة',
    ] : [
        'title' => 'FINANCIAL CLAIM', 'subtitle' => 'Payment Request',
        'claim_ref' => 'Claim Reference', 'date' => 'Date', 'tor' => 'ToR Number', 'currency' => 'Currency',
        'recipient' => 'Recipient Details', 'organization' => 'Organization / Institution', 'attention' => 'Attention', 'position' => 'Position',
        'details' => 'Claim Details', 'no' => 'No.', 'desc' => 'Description', 'amount' => 'Amount',
        'grand_total' => 'Grand Total', 'terms' => 'Terms & Notes', 'contact' => 'Contact Information',
        'mobile' => 'Mobile', 'whatsapp' => 'WhatsApp', 'location' => 'Location',
        'signature' => 'Authorized Signature', 'stamp' => 'Company Stamp',
    ];
    $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

    // dompdf بيرجع أي جزء عربي معكوس (باگ معروف بالمكتبة) — منعالجه هون قبل ما نطبع
    // أي نص، عربي كان أو مختلط، بدل ما نلاحقه بكل سطر لحاله.
    $ar = fn ($text) => \App\Support\ArabicPdfFix::fix($text);
    $labels = array_map($ar, $labels);
@endphp
<!DOCTYPE html>
<html lang="{{ $claim->language }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8">
<style>
    @font-face {
        font-family: 'Amiri'; font-style: normal; font-weight: normal;
        src: url('{{ resource_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
    }
    @font-face {
        font-family: 'Amiri'; font-style: normal; font-weight: bold;
        src: url('{{ resource_path('fonts/Amiri-Bold.ttf') }}') format('truetype');
    }
    * { font-family: {{ $isAr ? 'Amiri, sans-serif' : 'Arial, sans-serif' }}; box-sizing: border-box; }
    @page { size: A4; margin: 9mm; }
    body { direction: {{ $isAr ? 'rtl' : 'ltr' }}; text-align: {{ $isAr ? 'right' : 'left' }}; font-size: 11px; line-height: 1.25; color: #1f2937; margin: 0; padding: 0; }

    table.top { width: 100%; border-collapse: collapse; border-bottom: 3px solid #dc2626; padding-bottom: 6px; margin-bottom: 9px; }
    table.top td { vertical-align: middle; }
    .brand { font-size: 15px; font-weight: bold; }
    .brand .en { color: #dc2626; }
    .tag { background: #dc2626; color: #fff; padding: 5px 14px; font-weight: bold; font-size: 11px; }

    .doc-title { text-align: center; font-size: 17px; font-weight: bold; margin-bottom: 1px; line-height: 1.2; }
    .doc-subtitle { text-align: center; color: #6b7280; margin-bottom: 8px; font-size: 10px; }

    table.meta { width: 100%; border-collapse: collapse; margin-bottom: 9px; }
    table.meta td { border: 1px solid #cbd5e1; padding: 4px 9px; font-size: 11px; line-height: 1.2; }
    table.meta td.label { background: #f8fafc; font-weight: bold; width: 25%; }

    .section-title { font-weight: bold; font-size: 12px; margin: 9px 0 4px; line-height: 1.2; }

    table.items { width: 100%; border-collapse: collapse; }
    table.items th { background: #dc2626; color: #fff; padding: 5px; font-size: 11px; border: 1px solid #dc2626; }
    table.items td { border: 1px solid #cbd5e1; padding: 4px 8px; font-size: 11px; line-height: 1.2; }

    table.total { width: 100%; border-collapse: collapse; margin-top: 3px; }
    table.total td { padding: 6px 10px; font-size: 12px; line-height: 1.2; }
    table.total .grand { border: 2px solid #dc2626; background: #fef2f2; font-weight: bold; font-size: 13px; color: #dc2626; }

    .notes-box { margin-top: 8px; background: #f8fafc; border-{{ $isAr ? 'right' : 'left' }}: 4px solid #dc2626; padding: 7px 12px; font-size: 10px; line-height: 1.3; color: #475569; }

    table.contact { width: 100%; border-collapse: collapse; margin-top: 8px; }
    table.contact td { border: 1px solid #cbd5e1; padding: 4px 9px; font-size: 10px; text-align: center; line-height: 1.2; }
    table.contact td.label { background: #f8fafc; font-weight: bold; }

    table.sign { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.sign td { border: 1px solid #cbd5e1; padding: 5px; text-align: center; font-size: 10px; height: 38px; vertical-align: bottom; line-height: 1.2; }

    .footer { text-align: center; margin-top: 8px; padding-top: 5px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #6b7280; }
</style>
</head>
<body>

    <table class="top">
        <tr>
            @if($tenant && $tenant->logo_path)
                <td style="width: 14%;">
                    <img src="{{ public_path('storage/'.$tenant->logo_path) }}" style="max-height: 36px; max-width: 100%; object-fit: contain;">
                </td>
            @endif
            <td style="width: {{ $tenant && $tenant->logo_path ? '46%' : '60%' }};">
                @php
                    $brandName = $isAr
                        ? ($tenant->name ?? 'أونلاين سيل')
                        : ($tenant->name_en ?: ($tenant->name ?? 'Online Sale'));
                @endphp
                <div class="brand">{{ $ar($brandName) }}</div>
            </td>
            <td style="width: 40%; text-align: {{ $isAr ? 'left' : 'right' }};">
                <span class="tag">{{ $labels['title'] }}</span>
            </td>
        </tr>
    </table>

    <div class="doc-title">{{ $labels['title'] }}</div>
    <div class="doc-subtitle">{{ $labels['subtitle'] }}</div>

    <table class="meta">
        <tr>
            <td class="label">{{ $labels['claim_ref'] }}</td>
            <td>{{ $claim->claim_reference }}</td>
            <td class="label">{{ $labels['date'] }}</td>
            <td>{{ $claim->claim_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">{{ $labels['tor'] }}</td>
            <td>{{ $claim->tor_number ?: '-' }}</td>
            <td class="label">{{ $labels['currency'] }}</td>
            <td>{{ $claim->currency }}</td>
        </tr>
    </table>

    <div class="section-title">{{ $labels['recipient'] }}</div>
    <table class="meta">
        <tr>
            <td class="label">{{ $labels['organization'] }}</td>
            <td colspan="3">{{ $ar($claim->organization_name) }}</td>
        </tr>
        @if($claim->attention_name)
        <tr><td class="label">{{ $labels['attention'] }}</td><td colspan="3">{{ $ar($claim->attention_name) }}</td></tr>
        @endif
        @if($claim->position)
        <tr><td class="label">{{ $labels['position'] }}</td><td colspan="3">{{ $ar($claim->position) }}</td></tr>
        @endif
    </table>

    <div class="section-title">{{ $labels['details'] }}</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 10%;">{{ $labels['no'] }}</th>
                <th>{{ $labels['desc'] }}</th>
                <th style="width: 25%;">{{ $labels['amount'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($claim->items as $item)
                <tr>
                    <td style="text-align:center;">{{ $item->item_number }}</td>
                    <td>{{ $ar($item->description) }}</td>
                    <td style="text-align:center;">{{ number_format($item->amount, 2) }} {{ $claim->currency }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total">
        <tr class="grand">
            <td style="text-align: {{ $isAr ? 'left' : 'right' }};">{{ $labels['grand_total'] }}</td>
            <td style="text-align:center; width: 30%;">{{ number_format($claim->total_amount, 2) }} {{ $claim->currency }}</td>
        </tr>
    </table>

    @if($claim->notes)
        <div class="section-title">{{ $labels['terms'] }}</div>
        <div class="notes-box">{!! nl2br(e($ar($claim->notes))) !!}</div>
    @endif

    <div class="section-title">{{ $labels['contact'] }}</div>
    <table class="contact">
        <tr>
            <td class="label">{{ $labels['mobile'] }}</td>
            <td class="label">{{ $labels['whatsapp'] }}</td>
            <td class="label">{{ $labels['location'] }}</td>
            @if($tenant && $tenant->contact_email)
                <td class="label">{{ $isAr ? $ar('البريد الإلكتروني') : 'Email' }}</td>
            @endif
        </tr>
        <tr>
            <td>{{ $tenant->contact_phone ?? '0597848937' }}</td>
            <td>{{ $tenant->contact_whatsapp ?? '00970592552702' }}</td>
            @php
                $displayAddress = $isAr
                    ? ($tenant->contact_address ?: 'خانيونس - شمال مفترق النص')
                    : ($tenant->contact_address_en ?: ($tenant->contact_address ?: 'Khan Younis - North of Al-Nisf Junction'));
            @endphp
            <td>{{ $ar($displayAddress) }}</td>
            @if($tenant && $tenant->contact_email)
                <td>{{ $tenant->contact_email }}</td>
            @endif
        </tr>
    </table>

    <table class="sign">
        <tr>
            <td style="width: 50%;">
                {{ $labels['signature'] }}
                @if($tenant && $tenant->signature_path)
                    <br><img src="{{ public_path('storage/'.$tenant->signature_path) }}" style="max-height: 45px; margin-top: 4px;">
                @endif
            </td>
            <td style="width: 50%;">
                {{ $labels['stamp'] }}<br>
                <img src="{{ $tenant && $tenant->stamp_path ? public_path('storage/'.$tenant->stamp_path) : public_path('assets/logo/stamping.png') }}" style="max-height: 45px; opacity: .85; margin-top: 4px;">
            </td>
        </tr>
    </table>

    @php
        $footerName = $isAr ? ($tenant->name ?? 'Online Sale') : ($tenant->name_en ?: ($tenant->name ?? 'Online Sale'));
        $footerAddress = $isAr
            ? ($tenant->contact_address ?? null)
            : ($tenant->contact_address_en ?: ($tenant->contact_address ?? null));
    @endphp
    <div class="footer">{{ $ar($footerName) }}@if($footerAddress) | {{ $ar($footerAddress) }}@else | Khan Younis - 50 m north of Al-Nisf Junction @endif @if($tenant->contact_email ?? null) | {{ $tenant->contact_email }}@endif</div>

</body>
</html>
