<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Segoe UI', sans-serif;
            background: white;
            padding: 0;
            margin: 0;
        }

        .invoice-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            position: relative;
            padding: 20mm;
        }

        /* التصميم الهندسي في الخلفية */
        .background-design {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .triangle-top {
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 150px 150px 0;
            border-color: transparent #FF4757 transparent transparent;
        }

        .triangle-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 150px 0 0 150px;
            border-color: transparent transparent transparent #000;
        }

        .diagonal-line {
            position: absolute;
            top: -50px;
            left: -150px;
            width: 300px;
            height: 200px;
            background: #FFE5E8;
            transform: rotate(-25deg);
        }

        /* المحتوى */
        .content {
            position: relative;
            z-index: 1;
        }

        /* الهيدر */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #FF4757;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-box {
            border: 4px solid #FF4757;
            border-radius: 10px;
            padding: 15px;
            background: white;
        }

        .store-name-section {
            text-align: center;
        }

        .store-title-ar {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .store-title-en {
            font-size: 24px;
            color: #FF4757;
            font-weight: bold;
            letter-spacing: 3px;
        }

        .invoice-title-box {
            background: #FF4757;
            color: white;
            padding: 15px 30px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            border-radius: 0 50px 0 50px;
        }

        /* معلومات الفاتورة */
        .info-section {
            margin: 30px 0;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .info-box {
            flex: 1;
            padding: 15px;
            background: #f8f9fa;
            border-right: 4px solid #FF4757;
            border-radius: 5px;
        }

        .info-label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* جدول المنتجات */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .items-table th {
            padding: 15px 10px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .items-table td {
            padding: 12px 10px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .items-table tbody tr:hover {
            background: #e8f4f8;
        }

        /* صفوف الحسابات */
        .subtotal-row {
            background: #f8f9fa !important;
        }

        .subtotal-row td {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
            border: 2px solid #e0e0e0;
        }

        .discount-row {
            background: #fff9e6 !important;
        }

        .discount-row td {
            font-weight: bold;
            font-size: 16px;
            color: #f39c12;
            border: 2px solid #f39c12;
        }

        .total-row {
            background: #e8f5e9 !important;
        }

        .total-row td {
            font-weight: bold;
            font-size: 20px;
            color: #27ae60;
            border: 3px solid #27ae60;
            padding: 15px 10px;
        }

        /* الملاحظات */
        .notes-section {
            margin: 30px 0;
            padding: 15px;
            background: #fffacd;
            border-right: 4px solid #f39c12;
            border-radius: 5px;
        }

        .notes-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .notes-content {
            color: #555;
            line-height: 1.6;
            font-size: 14px;
        }

        /* الفوتر */
        .footer {
            position: absolute;
            bottom: 20mm;
            left: 20mm;
            right: 20mm;
            padding-top: 20px;
            border-top: 2px solid #FF4757;
        }

        .contact-info {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #2c3e50;
        }

        .contact-icon {
            width: 30px;
            height: 30px;
            background: #FF4757;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .address {
            text-align: center;
            color: #555;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .address-icon {
            color: #FF4757;
            font-size: 16px;
        }

        /* أزرار الطباعة */
        .print-buttons {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .btn {
            padding: 12px 30px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: #27ae60;
            color: white;
        }

        .btn-print:hover {
            background: #229954;
        }

        .btn-back {
            background: #95a5a6;
            color: white;
        }

        .btn-back:hover {
            background: #7f8c8d;
        }

        @media print {
            .print-buttons {
                display: none;
            }

            .invoice-container {
                width: 100%;
                padding: 15mm;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body>
    <div class="print-buttons">
        <button onclick="window.print()" class="btn btn-print">🖨️ طباعة الفاتورة</button>
        <a href="{{ route('invoices.index') }}" class="btn btn-back">↩️ العودة للقائمة</a>
    </div>

    <div class="invoice-container">
        <!-- التصميم الهندسي في الخلفية -->
        <div class="background-design">
            <div class="triangle-top"></div>
            <div class="triangle-bottom"></div>
            <div class="diagonal-line"></div>
        </div>

        <!-- المحتوى -->
        <div class="content">
            <!-- الهيدر -->
            <div class="header">
                <div class="logo-section">
                    <div class="logo-box">
                        <img src="{{ asset('assets/logo/logo.png') }}" alt="Logo" />
                    </div>
                </div>

                <div class="store-name-section">
                    <div class="store-title-ar">فـــاتـــورة</div>
                    <div class="store-title-en">INVOICE</div>
                </div>

                <div class="invoice-title-box">
                    <div class="store-title-ar" style="font-size: 14px; margin: 0 0 15px 0; text-align: left;">Online Sale
                         - أونلاين سيل</div>
                    <div class="store-title-ar" style="font-size: 14px; margin: 0 0 15px 0; text-align: left;">
                        العنوان: خانيونس - شمال مفترق النص <br>- بجانب مجوهرات الترتوري
                    </div>
                    <div class="store-title-ar" style="font-size: 14px; margin: 0; text-align: left;">رقم الهاتف:
                        0597848937</div>
                </div>
            </div>

            <!-- معلومات الفاتورة -->
            <div class="info-section">
                <div class="info-box">
                    <div class="info-label">اسم العميل:</div>
                    <div class="info-value">{{ $invoice->customer_name }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">التاريخ:</div>
                    <div class="info-value">{{ $invoice->invoice_date->format('Y-m-d') }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">رقم الفاتورة:</div>
                    <div class="info-value">{{ $invoice->invoice_number }}</div>
                </div>
            </div>

            <!-- جدول المنتجات -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>رقم</th>
                        <th>وصف المنتج</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $item->item_number }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach

                    @php
                        $emptyRows = 8 - $invoice->items->count();
                    @endphp

                    @for ($i = 0; $i < $emptyRows; $i++)
                        <tr>
                            <td>{{ $invoice->items->count() + $i + 1 }}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor

                    <!-- الإجمالي قبل الخصم -->
                    <tr class="subtotal-row">
                        <td colspan="4" style="text-align: left; padding-right: 20px;">الإجمالي قبل الخصم</td>
                        <td>{{ number_format($invoice->total_amount, 2) }} شيكل</td>
                    </tr>

                    <!-- الخصم -->
                    @if($invoice->discount_amount > 0)
                    <tr class="discount-row">
                        <td colspan="4" style="text-align: left; padding-right: 20px;">الخصم</td>
                        <td>{{ number_format($invoice->discount_amount, 2) }} شيكل</td>
                    </tr>
                    @endif

                    <!-- الإجمالي النهائي -->
                    <tr class="total-row">
                        <td colspan="4" style="text-align: left; padding-right: 20px;">
                            <span style="font-size: 22px;">💰</span> إجمالي المطلوب
                        </td>
                        <td>{{ number_format($invoice->afterDiscount_amount, 2) }} شيكل</td>
                    </tr>
                </tbody>
            </table>

            <!-- الملاحظات -->
            @if ($invoice->notes)
                <div class="notes-section">
                    <div class="notes-title">ملاحظات:</div>
                    <div class="notes-content">{{ $invoice->notes }}</div>
                </div>
            @endif
        </div>

        <!-- الفوتر -->
        <div class="footer">
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <span>059-784-8937</span>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">💬</div>
                    <span>+970592552702</span>
                </div>
            </div>

            <div class="address">
                <span class="address-icon">📍</span>
                <span>خانيونس - شمال مفترق النص - بجانب مجوهرات الترتوري</span>
            </div>
        </div>
    </div>
</body>

</html>