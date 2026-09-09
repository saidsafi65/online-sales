@php
    $isAr = $quote->language === 'ar';
    $labels = $isAr ? [
        'title' => 'عرض سعر', 'subtitle' => 'Price Quote',
        'quote_ref' => 'رقم العرض', 'date' => 'التاريخ', 'valid_until' => 'صالح حتى', 'currency' => 'العملة',
        'client' => 'بيانات العميل', 'client_name' => 'الاسم', 'client_phone' => 'الهاتف', 'client_email' => 'الإيميل',
        'details' => 'تفاصيل العرض', 'no' => 'رقم', 'desc' => 'الوصف', 'qty' => 'الكمية', 'unit_price' => 'السعر', 'amount' => 'الإجمالي',
        'subtotal' => 'الإجمالي قبل الخصم', 'discount' => 'الخصم', 'grand_total' => 'الإجمالي النهائي',
        'terms' => 'الشروط والملاحظات', 'contact' => 'معلومات التواصل',
        'mobile' => 'الجوال', 'whatsapp' => 'واتساب', 'location' => 'الموقع',
        'signature' => 'التوقيع المعتمد', 'stamp' => 'ختم الشركة',
        'expired' => 'هذا العرض منتهي الصلاحية',
    ] : [
        'title' => 'PRICE QUOTE', 'subtitle' => 'Official Quotation',
        'quote_ref' => 'Quote Number', 'date' => 'Date', 'valid_until' => 'Valid Until', 'currency' => 'Currency',
        'client' => 'Client Details', 'client_name' => 'Name', 'client_phone' => 'Phone', 'client_email' => 'Email',
        'details' => 'Quote Details', 'no' => 'No.', 'desc' => 'Description', 'qty' => 'Qty', 'unit_price' => 'Unit Price', 'amount' => 'Amount',
        'subtotal' => 'Subtotal', 'discount' => 'Discount', 'grand_total' => 'Grand Total',
        'terms' => 'Terms & Notes', 'contact' => 'Contact Information',
        'mobile' => 'Mobile', 'whatsapp' => 'WhatsApp', 'location' => 'Location',
        'signature' => 'Authorized Signature', 'stamp' => 'Company Stamp',
        'expired' => 'This quote has expired',
    ];
    $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

    // dompdf بيرجع أي جزء عربي معكوس (باگ معروف بالمكتبة) — منعالجه هون قبل ما نطبع
    // أي نص، عربي كان أو مختلط.
    $ar = fn ($text) => \App\Support\ArabicPdfFix::fix($text);
    $labels = array_map($ar, $labels);
@endphp
<!DOCTYPE html>
<html lang="{{ $quote->language }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
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

    table.top { width: 100%; border-collapse: collapse; border-bottom: 3px solid #0ea5e9; padding-bottom: 6px; margin-bottom: 9px; }
    table.top td { vertical-align: middle; }
    .brand { font-size: 15px; font-weight: bold; }
    .tag { background: #0ea5e9; color: #fff; padding: 5px 14px; font-weight: bold; font-size: 11px; }

    .doc-title { text-align: center; font-size: 17px; font-weight: bold; margin-bottom: 1px; line-height: 1.2; }
    .doc-subtitle { text-align: center; color: #6b7280; margin-bottom: 8px; font-size: 10px; }

    .expired-banner { text-align: center; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 5px; font-weight: bold; font-size: 11px; margin-bottom: 8px; }

    table.meta { width: 100%; border-collapse: collapse; margin-bottom: 9px; }
    table.meta td { border: 1px solid #cbd5e1; padding: 4px 9px; font-size: 11px; line-height: 1.2; }
    table.meta td.label { background: #f8fafc; font-weight: bold; width: 25%; }

    .section-title { font-weight: bold; font-size: 12px; margin: 9px 0 4px; line-height: 1.2; }

    table.items { width: 100%; border-collapse: collapse; }
    table.items th { background: #0ea5e9; color: #fff; padding: 5px; font-size: 11px; border: 1px solid #0ea5e9; }
    table.items td { border: 1px solid #cbd5e1; padding: 4px 8px; font-size: 11px; line-height: 1.2; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }

    table.total { width: 100%; border-collapse: collapse; margin-top: 3px; }
    table.total td { padding: 6px 10px; font-size: 12px; line-height: 1.2; }
    table.total .grand { border: 2px solid #0ea5e9; background: #eff6ff; font-weight: bold; font-size: 13px; color: #0ea5e9; }

    .notes-box { margin-top: 8px; background: #f8fafc; border-{{ $isAr ? 'right' : 'left' }}: 4px solid #0ea5e9; padding: 7px 12px; font-size: 10px; line-height: 1.3; color: #475569; }

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
                        : ($tenant->name_en ?? ($tenant->name ?? 'Online Sale'));
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

    @if($quote->is_expired)
        <div class="expired-banner">⚠ {{ $labels['expired'] }}</div>
    @endif

    <table class="meta">
        <tr>
            <td class="label">{{ $labels['quote_ref'] }}</td>
            <td>{{ $quote->quote_number }}</td>
            <td class="label">{{ $labels['date'] }}</td>
            <td>{{ $quote->quote_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">{{ $labels['valid_until'] }}</td>
            <td>{{ $quote->valid_until ? $quote->valid_until->format('d M Y') : '-' }}</td>
            <td class="label">{{ $labels['currency'] }}</td>
            <td>{{ $quote->currency }}</td>
        </tr>
    </table>

    <div class="section-title">{{ $labels['client'] }}</div>
    <table class="meta">
        <tr>
            <td class="label">{{ $labels['client_name'] }}</td>
            <td colspan="3">{{ $ar($quote->client_name) }}</td>
        </tr>
        @if($quote->client_phone || $quote->client_email)
        <tr>
            @if($quote->client_phone)
                <td class="label">{{ $labels['client_phone'] }}</td>
                <td>{{ $quote->client_phone }}</td>
            @endif
            @if($quote->client_email)
                <td class="label">{{ $labels['client_email'] }}</td>
                <td>{{ $quote->client_email }}</td>
            @endif
        </tr>
        @endif
    </table>

    <div class="section-title">{{ $labels['details'] }}</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 8%;">{{ $labels['no'] }}</th>
                <th>{{ $labels['desc'] }}</th>
                <th style="width: 12%;">{{ $labels['qty'] }}</th>
                <th style="width: 17%;">{{ $labels['unit_price'] }}</th>
                <th style="width: 17%;">{{ $labels['amount'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
                <tr>
                    <td style="text-align:center;">{{ $item->item_number }}</td>
                    <td>{{ $ar($item->description) }}</td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:center;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align:center;">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total">
        <tr>
            <td style="text-align: {{ $isAr ? 'left' : 'right' }};">{{ $labels['subtotal'] }}</td>
            <td style="text-align:center; width: 30%;">{{ number_format($quote->total_amount, 2) }} {{ $quote->currency }}</td>
        </tr>
        @if($quote->discount_amount != 0)
        <tr>
            <td style="text-align: {{ $isAr ? 'left' : 'right' }}; color:#b45309;">{{ $labels['discount'] }}</td>
            <td style="text-align:center; color:#b45309;">{{ number_format($quote->discount_amount, 2) }} {{ $quote->currency }}</td>
        </tr>
        @endif
        <tr class="grand">
            <td style="text-align: {{ $isAr ? 'left' : 'right' }};">{{ $labels['grand_total'] }}</td>
            <td style="text-align:center;">{{ number_format($quote->afterDiscount_amount, 2) }} {{ $quote->currency }}</td>
        </tr>
    </table>

    @if($quote->notes)
        <div class="section-title">{{ $labels['terms'] }}</div>
        <div class="notes-box">{!! nl2br(e($ar($quote->notes))) !!}</div>
    @endif

    <div class="section-title">{{ $labels['contact'] }}</div>
    <table class="contact">
        <tr>
            <td class="label">{{ $labels['mobile'] }}</td>
            <td class="label">{{ $labels['whatsapp'] }}</td>
            <td class="label">{{ $labels['location'] }}</td>
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
