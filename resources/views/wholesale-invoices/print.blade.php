<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة جملة - {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 12mm; }
        body { font-family: 'Arial', 'Segoe UI', sans-serif; background: #f1f5f9; padding: 20px; }
        .doc { max-width: 210mm; margin: 0 auto; background: white; box-shadow: 0 4px 20px rgba(0,0,0,.08); }

        .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 14px 24px; border-bottom: 1px solid #e2e8f0; }
        .brand-ar { font-size: 17px; font-weight: bold; color: #1e293b; }
        .brand-tag { background: #059669; color: white; padding: 7px 16px; font-weight: bold; font-size: 12px; letter-spacing: 1px; }

        .doc-body { padding: 20px 24px; }
        .doc-title { text-align: center; font-size: 22px; font-weight: 900; color: #1e293b; margin-bottom: 2px; }
        .doc-subtitle { text-align: center; color: #64748b; margin-bottom: 16px; font-size: 12px; }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.meta td { border: 1px solid #cbd5e1; padding: 7px 11px; font-size: 12px; }
        table.meta td.label { background: #f8fafc; font-weight: bold; width: 22%; }

        .section-title { font-weight: bold; font-size: 14px; margin: 14px 0 7px; color: #1e293b; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.items th { background: #059669; color: white; padding: 8px; font-size: 12px; border: 1px solid #059669; }
        table.items td { border: 1px solid #cbd5e1; padding: 7px 11px; font-size: 12px; }
        table.items tbody tr:nth-child(even) { background: #f8fafc; }

        table.total { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.total td { padding: 8px 12px; font-size: 13px; }
        table.total .grand { border: 2px solid #059669; background: #ecfdf5; font-weight: 900; font-size: 15px; }
        table.total .grand .amt { color: #059669; }
        table.total .label-cell { text-align: left; }

        .notes-box { margin-top: 12px; background: #f8fafc; border-right: 4px solid #059669; padding: 9px 15px; font-size: 12px; color: #475569; line-height: 1.5; }

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
        <button onclick="window.print()" class="btn btn-print">🖨️ طباعة</button>
        <a href="{{ route('wholesale-invoices.index') }}" class="btn btn-back">↩️ العودة</a>
    </div>

    @php $tenant = app()->bound('currentTenant') ? app('currentTenant') : null; @endphp
    <div class="doc">
        <div class="top-bar">
            <div style="display:flex; align-items:center; gap:.6rem;">
                @if($tenant && $tenant->logo_path)
                    <img src="{{ asset('storage/'.$tenant->logo_path) }}" alt="Logo" style="max-height:34px; max-width:90px; object-fit:contain;">
                @endif
                <div class="brand-ar">{{ $tenant->name ?? 'أونلاين سيل' }}</div>
            </div>
            <div class="brand-tag">فاتورة بيع بالجملة</div>
        </div>

        <div class="doc-body">
            <div class="doc-title">فاتورة بيع بالجملة</div>
            <div class="doc-subtitle">WHOLESALE INVOICE</div>

            <table class="meta">
                <tr>
                    <td class="label">المحل / المعرض المشتري</td>
                    <td colspan="3">{{ $invoice->buyer_store_name }}</td>
                </tr>
                @if($invoice->buyer_tax_number)
                <tr>
                    <td class="label">الرقم الضريبي</td>
                    <td colspan="3">{{ $invoice->buyer_tax_number }}</td>
                </tr>
                @endif
                @if($invoice->buyer_phone || $invoice->buyer_address)
                <tr>
                    @if($invoice->buyer_phone)
                        <td class="label">هاتف المحل</td>
                        <td>{{ $invoice->buyer_phone }}</td>
                    @endif
                    @if($invoice->buyer_address)
                        <td class="label">العنوان</td>
                        <td>{{ $invoice->buyer_address }}</td>
                    @endif
                </tr>
                @endif
                <tr>
                    <td class="label">رقم الفاتورة</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td class="label">التاريخ</td>
                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="label">شروط الدفع</td>
                    <td>{{ \App\Models\WholesaleInvoice::PAYMENT_TERMS_LABELS[$invoice->payment_terms] }}</td>
                    @if($invoice->payment_terms === 'credit' && $invoice->due_date)
                        <td class="label">تاريخ الاستحقاق</td>
                        <td>{{ $invoice->due_date->format('d M Y') }}</td>
                    @else
                        <td colspan="2"></td>
                    @endif
                </tr>
            </table>

            <div class="section-title">تفاصيل الفاتورة</div>
            <table class="items">
                <thead>
                    <tr>
                        <th style="width: 8%;">رقم</th>
                        <th>الوصف</th>
                        <th style="width: 12%;">الكمية</th>
                        <th style="width: 17%;">السعر</th>
                        <th style="width: 17%;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
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
                    <td class="label-cell" style="width: 70%;">الإجمالي قبل الخصم</td>
                    <td style="text-align:center;">{{ number_format($invoice->total_amount, 2) }} شيكل</td>
                </tr>
                @if($invoice->discount_amount != 0)
                <tr>
                    <td class="label-cell" style="color:#b45309;">الخصم</td>
                    <td style="text-align:center; color:#b45309;">{{ number_format($invoice->discount_amount, 2) }} شيكل</td>
                </tr>
                @endif
                <tr class="grand">
                    <td class="label-cell">الإجمالي النهائي</td>
                    <td class="amt" style="text-align:center;">{{ number_format($invoice->afterDiscount_amount, 2) }} شيكل</td>
                </tr>
            </table>

            @if($invoice->notes)
                <div class="section-title">ملاحظات</div>
                <div class="notes-box">{!! nl2br(e($invoice->notes)) !!}</div>
            @endif

            <table class="sign">
                <tr>
                    <td style="width: 50%;">
                        <span class="cap">التوقيع المعتمد</span>
                        @if($tenant && $tenant->signature_path)
                            <img class="stamp" src="{{ asset('storage/'.$tenant->signature_path) }}" alt="signature">
                        @endif
                    </td>
                    <td style="width: 50%;">
                        <span class="cap">ختم المعرض</span>
                        <img class="stamp" src="{{ $tenant && $tenant->stamp_path ? asset('storage/'.$tenant->stamp_path) : asset('assets/logo/stamping.png') }}" alt="stamp">
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            {{ $tenant->name ?? 'Online Sale' }} | {{ $tenant->contact_address ?? 'خانيونس - شمال مفترق النص' }} | {{ $tenant->contact_phone ?? '0597848937' }}
        </div>
    </div>

</body>
</html>
