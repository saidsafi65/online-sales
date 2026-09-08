@php
    $isAr = $claim->language === 'ar';
    $labels = $isAr ? [
        'title' => 'مطالبة مالية',
        'subtitle' => 'طلب دفع',
        'claim_ref' => 'رقم المطالبة',
        'date' => 'التاريخ',
        'tor' => 'رقم ToR',
        'currency' => 'العملة',
        'recipient' => 'بيانات المستلم',
        'organization' => 'المؤسسة / الجهة',
        'attention' => 'للعناية',
        'position' => 'المنصب',
        'details' => 'تفاصيل المطالبة',
        'no' => 'رقم',
        'desc' => 'الوصف',
        'amount' => 'المبلغ',
        'grand_total' => 'الإجمالي الكلي',
        'total_word' => 'فقط',
        'terms' => 'الشروط والملاحظات',
        'contact' => 'معلومات التواصل',
        'mobile' => 'الجوال',
        'whatsapp' => 'واتساب',
        'location' => 'الموقع',
        'signature' => 'التوقيع المعتمد',
        'stamp' => 'ختم الشركة',
    ] : [
        'title' => 'FINANCIAL CLAIM',
        'subtitle' => 'Payment Request',
        'claim_ref' => 'Claim Reference',
        'date' => 'Date',
        'tor' => 'ToR Number',
        'currency' => 'Currency',
        'recipient' => 'Recipient Details',
        'organization' => 'Organization / Institution',
        'attention' => 'Attention',
        'position' => 'Position',
        'details' => 'Claim Details',
        'no' => 'No.',
        'desc' => 'Description',
        'amount' => 'Amount',
        'grand_total' => 'Grand Total',
        'total_word' => 'ONLY',
        'terms' => 'Terms & Notes',
        'contact' => 'Contact Information',
        'mobile' => 'Mobile',
        'whatsapp' => 'WhatsApp',
        'location' => 'Location',
        'signature' => 'Authorized Signature',
        'stamp' => 'Company Stamp',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $claim->language }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $labels['title'] }} - {{ $claim->claim_reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 12mm; }
        body { font-family: 'Arial', 'Segoe UI', sans-serif; background: #f1f5f9; padding: 20px; }
        .doc { max-width: 210mm; margin: 0 auto; background: white; box-shadow: 0 4px 20px rgba(0,0,0,.08); }

        .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 12px 22px; border-bottom: 1px solid #e2e8f0; }
        .brand-ar { font-size: 17px; font-weight: bold; color: #1e293b; }
        .brand-tag { background: #dc2626; color: white; padding: 7px 16px; font-weight: bold; font-size: 12px; letter-spacing: 1px; }

        .doc-body { padding: 18px 22px; }
        .doc-title { text-align: center; font-size: 20px; font-weight: 900; color: #1e293b; margin-bottom: 2px; }
        .doc-subtitle { text-align: center; color: #64748b; margin-bottom: 14px; font-size: 12px; }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; }
        table.meta td.label { background: #f8fafc; font-weight: bold; width: 25%; }

        .section-title { font-weight: bold; font-size: 13px; margin: 12px 0 6px; color: #1e293b; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.items th { background: #dc2626; color: white; padding: 7px; font-size: 12px; border: 1px solid #dc2626; }
        table.items td { border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; }
        table.items tbody tr:nth-child(even) { background: #f8fafc; }

        table.total { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.total td { padding: 7px 12px; font-size: 12px; }
        table.total .grand { border: 2px solid #dc2626; background: #fef2f2; font-weight: 900; font-size: 14px; }
        table.total .grand .amt { color: #dc2626; }
        table.total .label-cell { text-align: {{ $isAr ? 'left' : 'right' }}; }

        .notes-box { margin-top: 10px; background: #f8fafc; border-{{ $isAr ? 'right' : 'left' }}: 4px solid #dc2626; padding: 8px 14px; font-size: 11px; color: #475569; line-height: 1.5; }

        table.contact { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.contact td { border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 11px; text-align: center; }
        table.contact td.label { background: #f8fafc; font-weight: bold; }

        table.sign { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.sign td { border: 1px solid #cbd5e1; padding: 8px; text-align: center; font-size: 11px; height: 55px; vertical-align: bottom; }
        table.sign .cap { font-weight: bold; margin-bottom: 22px; display: block; }
        table.sign img.stamp { max-height: 45px; opacity: .85; }

        .footer { text-align: center; padding: 8px; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; }

        .print-buttons { max-width: 210mm; margin: 0 auto 14px; text-align: center; }
        .btn { padding: 10px 26px; margin: 0 6px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-print { background: #059669; color: white; }
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
        <a href="{{ route('financial-claims.index') }}" class="btn btn-back">↩️ {{ $isAr ? 'العودة' : 'Back' }}</a>
    </div>

    <div class="doc">
        <div class="top-bar">
            <div class="brand-ar">Online Sale <span style="color:#dc2626;">أونلاين سيل</span></div>
            <div class="brand-tag">{{ $labels['title'] }}</div>
        </div>

        <div class="doc-body">
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
                    <td colspan="3">{{ $claim->organization_name }}</td>
                </tr>
                @if($claim->attention_name)
                <tr>
                    <td class="label">{{ $labels['attention'] }}</td>
                    <td colspan="3">{{ $claim->attention_name }}</td>
                </tr>
                @endif
                @if($claim->position)
                <tr>
                    <td class="label">{{ $labels['position'] }}</td>
                    <td colspan="3">{{ $claim->position }}</td>
                </tr>
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
                            <td>{{ $item->description }}</td>
                            <td style="text-align:center;">{{ number_format($item->amount, 2) }} {{ $claim->currency }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="total">
                <tr class="grand">
                    <td class="label-cell">{{ $labels['grand_total'] }}</td>
                    <td class="amt" style="text-align:center; width: 30%;">{{ number_format($claim->total_amount, 2) }} {{ $claim->currency }}</td>
                </tr>
            </table>

            @if($claim->notes)
                <div class="section-title">{{ $labels['terms'] }}</div>
                <div class="notes-box">{{ $claim->notes }}</div>
            @endif

            <div class="section-title">{{ $labels['contact'] }}</div>
            @php $tenant = app()->bound('currentTenant') ? app('currentTenant') : null; @endphp
            <table class="contact">
                <tr>
                    <td class="label">{{ $labels['mobile'] }}</td>
                    <td class="label">{{ $labels['whatsapp'] }}</td>
                    <td class="label">{{ $labels['location'] }}</td>
                </tr>
                <tr>
                    <td>{{ $tenant->contact_phone ?? '0597848937' }}</td>
                    <td>{{ $tenant->contact_whatsapp ?? '00970592552702' }}</td>
                    <td>{{ $isAr ? 'خانيونس - شمال مفترق النص' : 'Khan Younis - North of Al-Nisf Junction' }}</td>
                </tr>
            </table>

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

        <div class="footer">
            Online Sale | Laptop Sales &amp; Services | Khan Younis – 50 m north of Al-Nisf Junction
        </div>
    </div>

</body>
</html>
