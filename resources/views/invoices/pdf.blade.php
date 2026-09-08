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

    .header { border-bottom: 3px solid #b91c1c; padding-bottom: 8px; margin-bottom: 12px; }
    .header table { width: 100%; border-collapse: collapse; }
    .header .store-ar { font-size: 18px; font-weight: bold; color: #1f2937; }
    .header .store-en { font-size: 14px; font-weight: bold; color: #b91c1c; letter-spacing: 1px; }
    .header .title-box {
        background: #b91c1c; color: #fff; padding: 7px 16px; font-size: 16px; font-weight: bold;
        text-align: center; border-radius: 0 18px 0 18px;
    }

    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .info-table td {
        background: #f8f9fa; border-right: 4px solid #b91c1c; padding: 7px 12px; width: 33.33%;
    }
    .info-label { font-size: 10px; color: #6b7280; margin-bottom: 2px; }
    .info-value { font-size: 12px; font-weight: bold; color: #1f2937; }

    table.items { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.items thead { background: #b91c1c; color: #fff; }
    table.items th { padding: 7px 6px; text-align: center; font-size: 12px; border: 1px solid #ddd; }
    table.items td { padding: 6px 6px; text-align: center; border: 1px solid #ddd; font-size: 11px; }
    table.items tbody tr:nth-child(even) { background: #f8f9fa; }

    .subtotal-row td { font-weight: bold; background: #f8f9fa; border: 2px solid #e5e7eb; }
    .discount-row td { font-weight: bold; background: #fff9e6; color: #b45309; border: 2px solid #f59e0b; }
    .total-row td { font-weight: bold; font-size: 13px; background: #ecfdf5; color: #059669; border: 2px solid #059669; padding: 7px 6px; }

    .notes { margin-top: 12px; padding: 8px 12px; background: #fffbeb; border-right: 4px solid #f59e0b; font-size: 11px; }
    .notes .title { font-weight: bold; margin-bottom: 4px; }

    .footer { margin-top: 16px; padding-top: 8px; border-top: 2px solid #b91c1c; text-align: center; font-size: 10px; color: #4b5563; }

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
                    <div class="store-ar">Online Sale - {{ $ar('أونلاين سيل') }}</div>
                    <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">{{ $ar('خانيونس - شمال مفترق النص - بجانب مجوهرات الترتوري') }}</div>
                    <div style="font-size: 11px; color: #6b7280;">{{ $ar('هاتف: 0597848937') }}</div>
                </td>
                <td style="width: 45%; text-align: left;">
                    <span class="title-box">{{ $ar('فـاتـورة') }} / INVOICE</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">{{ $ar('اسم العميل') }}</div>
                <div class="info-value">{{ $ar($invoice->customer_name) }}</div>
            </td>
            <td>
                <div class="info-label">{{ $ar('التاريخ') }}</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</div>
            </td>
            <td>
                <div class="info-label">{{ $ar('رقم الفاتورة') }}</div>
                <div class="info-value">{{ $invoice->invoice_number }}</div>
            </td>
        </tr>
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
        📞 059-784-8937&nbsp;&nbsp;&nbsp;💬 +970592552702&nbsp;&nbsp;&nbsp;📍 {{ $ar('خانيونس - شمال مفترق النص - بجانب مجوهرات الترتوري') }}
    </div>

</body>
</html>
