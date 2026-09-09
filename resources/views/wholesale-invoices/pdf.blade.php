@php
    // dompdf بيرجع أي جزء عربي معكوس (باگ معروف بالمكتبة) — منعالجه هون قبل ما نطبع
    // أي نص، عربي كان أو مختلط.
    $ar = fn ($text) => \App\Support\ArabicPdfFix::fix($text);
    $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<style>
    @font-face {
        font-family: 'Amiri';
        font-style: normal;
        font-weight: normal;
        src: url('{{ resource_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
    }
    @font-face {
        font-family: 'Amiri';
        font-style: normal;
        font-weight: bold;
        src: url('{{ resource_path('fonts/Amiri-Bold.ttf') }}') format('truetype');
    }
    * { font-family: 'Amiri', sans-serif; box-sizing: border-box; }
    @page { size: A4; margin: 12mm; }
    body { direction: rtl; text-align: right; font-size: 12px; color: #1f2937; margin: 0; padding: 0; }

    .header { border-bottom: 3px solid #059669; padding-bottom: 8px; margin-bottom: 12px; }
    .header table { width: 100%; border-collapse: collapse; }
    .header .store-ar { font-size: 18px; font-weight: bold; color: #1f2937; }
    .header .title-box {
        background: #059669; color: #fff; padding: 7px 16px; font-size: 16px; font-weight: bold;
        text-align: center; border-radius: 0 18px 0 18px;
    }

    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .info-table td {
        background: #f8f9fa; border-right: 4px solid #059669; padding: 7px 12px; width: 25%;
    }
    .info-label { font-size: 10px; color: #6b7280; margin-bottom: 2px; }
    .info-value { font-size: 12px; font-weight: bold; color: #1f2937; }

    table.items { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.items thead { background: #059669; color: #fff; }
    table.items th { padding: 7px 6px; text-align: center; font-size: 12px; border: 1px solid #ddd; }
    table.items td { padding: 6px 6px; text-align: center; border: 1px solid #ddd; font-size: 11px; }
    table.items tbody tr:nth-child(even) { background: #f8f9fa; }

    .subtotal-row td { font-weight: bold; background: #f8f9fa; border: 2px solid #e5e7eb; }
    .discount-row td { font-weight: bold; background: #fff9e6; color: #b45309; border: 2px solid #f59e0b; }
    .total-row td { font-weight: bold; font-size: 13px; background: #ecfdf5; color: #059669; border: 2px solid #059669; padding: 7px 6px; }

    .notes { margin-top: 12px; padding: 8px 12px; background: #fffbeb; border-right: 4px solid #f59e0b; font-size: 11px; }
    .notes .title { font-weight: bold; margin-bottom: 4px; }

    .footer { margin-top: 16px; padding-top: 8px; border-top: 2px solid #059669; text-align: center; font-size: 10px; color: #4b5563; }

    table.sign { width: 100%; border-collapse: collapse; margin-top: 14px; }
    table.sign td { border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-size: 10px; height: 45px; vertical-align: bottom; }
    table.sign img { max-height: 45px; max-width: 100%; object-fit: contain; }
</style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                @if($tenant && $tenant->logo_path)
                    <td style="width: 16%;">
                        <img src="{{ public_path('storage/'.$tenant->logo_path) }}" style="max-height: 42px; max-width: 100%; object-fit: contain;">
                    </td>
                @endif
                <td style="width: {{ $tenant && $tenant->logo_path ? '49%' : '55%' }};">
                    <div class="store-ar">{{ $ar($tenant->name ?? 'أونلاين سيل') }}</div>
                    <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">{{ $ar($tenant->contact_address ?? 'خانيونس - شمال مفترق النص - بجانب مجوهرات الترتوري') }}</div>
                    <div style="font-size: 11px; color: #6b7280;">{{ $ar('هاتف: ' . ($tenant->contact_phone ?? '0597848937')) }}</div>
                </td>
                <td style="width: 45%; text-align: left;">
                    <span class="title-box">{{ $ar('فاتورة بيع بالجملة') }} / WHOLESALE INVOICE</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">{{ $ar('المحل المشتري') }}</div>
                <div class="info-value">{{ $ar($invoice->buyer_store_name) }}</div>
            </td>
            <td>
                <div class="info-label">{{ $ar('التاريخ') }}</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</div>
            </td>
            <td>
                <div class="info-label">{{ $ar('رقم الفاتورة') }}</div>
                <div class="info-value">{{ $invoice->invoice_number }}</div>
            </td>
            <td>
                <div class="info-label">{{ $ar('شروط الدفع') }}</div>
                <div class="info-value">
                    {{ $ar(\App\Models\WholesaleInvoice::PAYMENT_TERMS_LABELS[$invoice->payment_terms]) }}
                    @if($invoice->payment_terms === 'credit' && $invoice->due_date)
                        <br><span style="font-size: 10px; font-weight: normal;">{{ $ar('يستحق: ') }}{{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}</span>
                    @endif
                </div>
            </td>
        </tr>
        @if($invoice->buyer_tax_number || $invoice->buyer_phone)
        <tr>
            @if($invoice->buyer_tax_number)
                <td>
                    <div class="info-label">{{ $ar('الرقم الضريبي') }}</div>
                    <div class="info-value">{{ $invoice->buyer_tax_number }}</div>
                </td>
            @endif
            @if($invoice->buyer_phone)
                <td>
                    <div class="info-label">{{ $ar('هاتف المحل') }}</div>
                    <div class="info-value">{{ $invoice->buyer_phone }}</div>
                </td>
            @endif
        </tr>
        @endif
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 8%;">{{ $ar('رقم') }}</th>
                <th>{{ $ar('الوصف') }}</th>
                <th style="width: 12%;">{{ $ar('الكمية') }}</th>
                <th style="width: 15%;">{{ $ar('السعر') }}</th>
                <th style="width: 15%;">{{ $ar('الإجمالي') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->item_number }}</td>
                    <td style="text-align: right;">{{ $ar($item->description) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach

            <tr class="subtotal-row">
                <td colspan="4" style="text-align: left; padding-right: 14px;">{{ $ar('الإجمالي قبل الخصم') }}</td>
                <td>{{ number_format($invoice->total_amount, 2) }} {{ $ar('شيكل') }}</td>
            </tr>
            @if ($invoice->discount_amount != 0)
                <tr class="discount-row">
                    <td colspan="4" style="text-align: left; padding-right: 14px;">{{ $ar('الخصم') }}</td>
                    <td>{{ number_format($invoice->discount_amount, 2) }} {{ $ar('شيكل') }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td colspan="4" style="text-align: left; padding-right: 14px;">{{ $ar('إجمالي المطلوب') }}</td>
                <td>{{ number_format($invoice->afterDiscount_amount, 2) }} {{ $ar('شيكل') }}</td>
            </tr>
        </tbody>
    </table>

    @if ($invoice->notes)
        <div class="notes">
            <div class="title">{{ $ar('ملاحظات:') }}</div>
            <div>{!! nl2br(e($ar($invoice->notes))) !!}</div>
        </div>
    @endif

    <table class="sign">
        <tr>
            <td style="width: 50%;">
                {{ $ar('ختم المعرض') }}<br>
                <img src="{{ $tenant && $tenant->stamp_path ? public_path('storage/'.$tenant->stamp_path) : public_path('assets/logo/stamping.png') }}">
            </td>
            <td style="width: 50%;">
                {{ $ar('التوقيع المعتمد') }}
                @if($tenant && $tenant->signature_path)
                    <br><img src="{{ public_path('storage/'.$tenant->signature_path) }}">
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        📞 {{ $tenant->contact_phone ?? '059-784-8937' }}&nbsp;&nbsp;&nbsp;💬 {{ $tenant->contact_whatsapp ?? '+970592552702' }}&nbsp;&nbsp;&nbsp;📍 {{ $ar($tenant->contact_address ?? 'خانيونس - شمال مفترق النص - بجانب مجوهرات الترتوري') }}
        @if($tenant && $tenant->contact_email)
            &nbsp;&nbsp;&nbsp;✉️ {{ $tenant->contact_email }}
        @endif
    </div>

</body>
</html>
