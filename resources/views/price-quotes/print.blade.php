@php
    $isAr = $quote->language === 'ar';
    $labels = $isAr ? [
        'title' => 'عرض سعر',
        'subtitle' => 'Price Quote',
        'quote_ref' => 'رقم العرض',
        'date' => 'التاريخ',
        'valid_until' => 'صالح حتى',
        'currency' => 'العملة',
        'client' => 'بيانات العميل',
        'client_name' => 'الاسم',
        'client_phone' => 'الهاتف',
        'client_email' => 'الإيميل',
        'details' => 'تفاصيل العرض',
        'no' => 'رقم',
        'desc' => 'الوصف',
        'qty' => 'الكمية',
        'unit_price' => 'السعر',
        'amount' => 'الإجمالي',
        'subtotal' => 'الإجمالي قبل الخصم',
        'discount' => 'الخصم',
        'grand_total' => 'الإجمالي النهائي',
        'terms' => 'الشروط والملاحظات',
        'contact' => 'معلومات التواصل',
        'mobile' => 'الجوال',
        'whatsapp' => 'واتساب',
        'location' => 'الموقع',
        'signature' => 'التوقيع المعتمد',
        'stamp' => 'ختم الشركة',
        'expired' => 'هذا العرض منتهي الصلاحية',
    ] : [
        'title' => 'PRICE QUOTE',
        'subtitle' => 'Official Quotation',
        'quote_ref' => 'Quote Number',
        'date' => 'Date',
        'valid_until' => 'Valid Until',
        'currency' => 'Currency',
        'client' => 'Client Details',
        'client_name' => 'Name',
        'client_phone' => 'Phone',
        'client_email' => 'Email',
        'details' => 'Quote Details',
        'no' => 'No.',
        'desc' => 'Description',
        'qty' => 'Qty',
        'unit_price' => 'Unit Price',
        'amount' => 'Amount',
        'subtotal' => 'Subtotal',
        'discount' => 'Discount',
        'grand_total' => 'Grand Total',
        'terms' => 'Terms & Notes',
        'contact' => 'Contact Information',
        'mobile' => 'Mobile',
        'whatsapp' => 'WhatsApp',
        'location' => 'Location',
        'signature' => 'Authorized Signature',
        'stamp' => 'Company Stamp',
        'expired' => 'This quote has expired',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $quote->language }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $labels['title'] }} - {{ $quote->quote_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 12mm; }
        body { font-family: 'Arial', 'Segoe UI', sans-serif; background: #f1f5f9; padding: 20px; }
        .doc { max-width: 210mm; margin: 0 auto; background: white; box-shadow: 0 4px 20px rgba(0,0,0,.08); }

        .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 14px 24px; border-bottom: 1px solid #e2e8f0; }
        .brand-ar { font-size: 17px; font-weight: bold; color: #1e293b; }
        .brand-tag { background: #0ea5e9; color: white; padding: 7px 16px; font-weight: bold; font-size: 12px; letter-spacing: 1px; }

        .doc-body { padding: 20px 24px; }
        .doc-title { text-align: center; font-size: 22px; font-weight: 900; color: #1e293b; margin-bottom: 2px; }
        .doc-subtitle { text-align: center; color: #64748b; margin-bottom: 16px; font-size: 12px; }

        .expired-banner { text-align: center; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 8px; font-weight: bold; font-size: 13px; margin-bottom: 14px; border-radius: 6px; }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.meta td { border: 1px solid #cbd5e1; padding: 7px 11px; font-size: 12px; }
        table.meta td.label { background: #f8fafc; font-weight: bold; width: 22%; }

        .section-title { font-weight: bold; font-size: 14px; margin: 14px 0 7px; color: #1e293b; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.items th { background: #0ea5e9; color: white; padding: 8px; font-size: 12px; border: 1px solid #0ea5e9; }
        table.items td { border: 1px solid #cbd5e1; padding: 7px 11px; font-size: 12px; }
        table.items tbody tr:nth-child(even) { background: #f8fafc; }

        table.total { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.total td { padding: 8px 12px; font-size: 13px; }
        table.total .grand { border: 2px solid #0ea5e9; background: #eff6ff; font-weight: 900; font-size: 15px; }
        table.total .grand .amt { color: #0ea5e9; }
        table.total .label-cell { text-align: left; }

        .notes-box { margin-top: 12px; background: #f8fafc; border-right: 4px solid #0ea5e9; padding: 9px 15px; font-size: 12px; color: #475569; line-height: 1.5; }

        table.contact { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.contact td { border: 1px solid #cbd5e1; padding: 7px 11px; font-size: 12px; text-align: center; }
        table.contact td.label { background: #f8fafc; font-weight: bold; }

        table.sign { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.sign td { border: 1px solid #cbd5e1; padding: 9px; text-align: center; font-size: 12px; height: 60px; vertical-align: bottom; }
        table.sign .cap { font-weight: bold; margin-bottom: 24px; display: block; }
        table.sign img.stamp { max-height: 48px; opacity: .85; }

        .footer { text-align: center; padding: 9px; color: #94a3b8; font-size: 11px; border-top: 1px solid #e2e8f0; }

        .print-buttons { max-width: 210mm; margin: 0 auto 14px; text-align: center; }
        .btn { padding: 10px 26px; margin: 0 6px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-print { background: #0ea5e9; color: white; }
        .btn-back { background: #64748b; color: white; }

        @media print {
            body { background: white; padding: 0; }
            .print-buttons { display: none; }
            .doc { box-shadow: none; }
        }
    </style>
</head>
<body>

    <div class="print-buttons">
        <button onclick="window.print()" class="btn btn-print">🖨️ {{ $isAr ? 'طباعة' : 'Print' }}</button>
        <a href="{{ route('price-quotes.index') }}" class="btn btn-back">↩️ {{ $isAr ? 'العودة' : 'Back' }}</a>
    </div>

    @php $tenant = app()->bound('currentTenant') ? app('currentTenant') : null; @endphp
    <div class="doc">
        <div class="top-bar">
            <div style="display:flex; align-items:center; gap:.6rem;">
                @if($tenant && $tenant->logo_path)
                    <img src="{{ asset('storage/'.$tenant->logo_path) }}" alt="Logo" style="max-height:34px; max-width:90px; object-fit:contain;">
                @endif
                @php
                    $brandName = $isAr
                        ? ($tenant->name ?? 'أونلاين سيل')
                        : ($tenant->name_en ?? ($tenant->name ?? 'Online Sale'));
                @endphp
                <div class="brand-ar">{{ $brandName }}</div>
            </div>
            <div class="brand-tag">{{ $labels['title'] }}</div>
        </div>

        <div class="doc-body">
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
                    <td colspan="3">{{ $quote->client_name }}</td>
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
                            <td>{{ $item->description }}</td>
                            <td style="text-align:center;">{{ $item->quantity }}</td>
                            <td style="text-align:center;">{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align:center;">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="total">
                <tr>
                    <td class="label-cell" style="width: 70%;">{{ $labels['subtotal'] }}</td>
                    <td style="text-align:center;">{{ number_format($quote->total_amount, 2) }} {{ $quote->currency }}</td>
                </tr>
                @if($quote->discount_amount != 0)
                <tr>
                    <td class="label-cell" style="color:#b45309;">{{ $labels['discount'] }}</td>
                    <td style="text-align:center; color:#b45309;">{{ number_format($quote->discount_amount, 2) }} {{ $quote->currency }}</td>
                </tr>
                @endif
                <tr class="grand">
                    <td class="label-cell">{{ $labels['grand_total'] }}</td>
                    <td class="amt" style="text-align:center;">{{ number_format($quote->afterDiscount_amount, 2) }} {{ $quote->currency }}</td>
                </tr>
            </table>

            @if($quote->notes)
                <div class="section-title">{{ $labels['terms'] }}</div>
                <div class="notes-box">{!! nl2br(e($quote->notes)) !!}</div>
            @endif

            <table class="sign">
                <tr>
                    <td style="width: 50%;">
                        <span class="cap">{{ $labels['signature'] }}</span>
                        @if($tenant && $tenant->signature_path)
                            <img class="stamp" src="{{ asset('storage/'.$tenant->signature_path) }}" alt="signature">
                        @endif
                    </td>
                    <td style="width: 50%;">
                        <span class="cap">{{ $labels['stamp'] }}</span>
                        <img class="stamp" src="{{ $tenant && $tenant->stamp_path ? asset('storage/'.$tenant->stamp_path) : asset('assets/logo/stamping.png') }}" alt="stamp">
                    </td>
                </tr>
            </table>
        </div>

        @php
            $footerName = $isAr ? ($tenant->name ?? 'Online Sale') : ($tenant->name_en ?: ($tenant->name ?? 'Online Sale'));
            $footerAddress = $isAr
                ? ($tenant->contact_address ?? 'خانيونس - شمال مفترق النص')
                : ($tenant->contact_address_en ?: ($tenant->contact_address ?? 'Khan Younis – 50 m north of Al-Nisf Junction'));
        @endphp
        <div class="footer">
            {{ $footerName }} | {{ $footerAddress }}@if($tenant && $tenant->contact_email) | {{ $tenant->contact_email }}@endif
        </div>
    </div>

</body>
</html>
