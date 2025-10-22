<!-- resources/views/invoices/receipt.blade.php (سند القبض) -->
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض - {{ $invoice->invoice_number }}</title>
    <style>
        @page { size: A5 landscape; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', 'Segoe UI', sans-serif; 
            background: white; 
            padding: 0; 
            margin: 0; 
        }
        
        .receipt-container {
            width: 210mm;
            height: 148mm;
            margin: 0 auto;
            background: white;
            position: relative;
            padding: 15mm;
            display: flex;
            flex-direction: column;
        }

        /* الختم */
        .stamp-container {
            position: absolute;
            bottom: 25mm;
            left: 20mm;
            width: 100px;
            height: 100px;
            opacity: 0.6;
            transform: rotate(-15deg);
            z-index: 10;
        }

        .stamp-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* الهيدر */
        .receipt-header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 3px dashed #FF4757;
            margin-bottom: 20px;
        }

        .receipt-title {
            font-size: 36px;
            font-weight: 900;
            color: #FF4757;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .receipt-subtitle {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 600;
        }

        .receipt-number {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 25px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        /* المحتوى */
        .receipt-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-row {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-right: 5px solid #FF4757;
            border-radius: 8px;
        }

        .info-label {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            min-width: 180px;
        }

        .info-value {
            font-size: 18px;
            color: #555;
            flex: 1;
        }

        /* مربع المبلغ */
        .amount-box {
            text-align: center;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            padding: 25px;
            border-radius: 15px;
            border: 3px solid #10b981;
            margin: 20px 0;
        }

        .amount-label {
            font-size: 20px;
            color: #065f46;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 48px;
            font-weight: 900;
            color: #10b981;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .amount-words {
            font-size: 16px;
            color: #065f46;
            margin-top: 10px;
            font-style: italic;
        }

        /* التوقيعات */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 20px;
            border-top: 2px dashed #e0e0e0;
        }

        .signature-box {
            text-align: center;
            flex: 1;
        }

        .signature-line {
            width: 150px;
            height: 60px;
            border-bottom: 2px solid #2c3e50;
            margin: 0 auto 10px;
        }

        .signature-label {
            font-size: 14px;
            color: #555;
            font-weight: 600;
        }

        /* الفوتر */
        .receipt-footer {
            text-align: center;
            padding-top: 15px;
            border-top: 2px solid #FF4757;
            margin-top: 20px;
        }

        .footer-info {
            font-size: 12px;
            color: #555;
            line-height: 1.6;
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

        .btn-print { background: #27ae60; color: white; }
        .btn-print:hover { background: #229954; }
        .btn-back { background: #95a5a6; color: white; }
        .btn-back:hover { background: #7f8c8d; }

        @media print {
            .print-buttons { display: none; }
            .receipt-container { width: 100%; height: 100%; padding: 10mm; }
            body { background: white; }
        }
    </style>
</head>

<body>
    <div class="print-buttons">
        <button onclick="window.print()" class="btn btn-print">🖨️ طباعة سند القبض</button>
        <a href="{{ route('invoices.index') }}" class="btn btn-back">↩️ العودة للقائمة</a>
    </div>

    <div class="receipt-container">
        <!-- الختم -->
        <div class="stamp-container">
            <img src="{{ asset('assets/logo/stamping.png') }}" alt="ختم المعرض">
        </div>

        <!-- الهيدر -->
        <div class="receipt-header">
            <div class="receipt-title">سند قبض</div>
            <div class="receipt-subtitle">PAYMENT RECEIPT</div>
            <div class="receipt-number">رقم الفاتورة: {{ $invoice->invoice_number }}</div>
        </div>

        <!-- المحتوى -->
        <div class="receipt-content">
            <!-- معلومات العميل -->
            <div class="info-row">
                <div class="info-label">استلمنا من السيد/ة:</div>
                <div class="info-value">{{ $invoice->customer_name }}</div>
            </div>

            <!-- التاريخ -->
            <div class="info-row">
                <div class="info-label">التاريخ:</div>
                <div class="info-value">{{ $invoice->invoice_date->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}</div>
            </div>

            <!-- مربع المبلغ -->
            <div class="amount-box">
                <div class="amount-label">💰 المبلغ المستلم</div>
                <div class="amount-value">{{ number_format($invoice->afterDiscount_amount, 2) }} ₪</div>
                <div class="amount-words">
                    فقط {{ $this->numberToArabicWords($invoice->afterDiscount_amount) }} شيكل لا غير
                </div>
            </div>

            @if($invoice->discount_amount > 0)
            <div class="info-row" style="background: #fff9e6; border-right-color: #f39c12;">
                <div class="info-label">🏷️ الخصم المطبق:</div>
                <div class="info-value" style="color: #f39c12; font-weight: 700;">{{ number_format($invoice->discount_amount, 2) }} ₪</div>
            </div>
            @endif

            <!-- السبب -->
            <div class="info-row">
                <div class="info-label">وذلك عن:</div>
                <div class="info-value">
                    @if($invoice->notes)
                        {{ $invoice->notes }}
                    @else
                        دفعة مقابل {{ $invoice->items->count() }} منتج/منتجات حسب الفاتورة رقم {{ $invoice->invoice_number }}
                    @endif
                </div>
            </div>
        </div>

        <!-- التوقيعات -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">توقيع المستلم</div>
                <div style="font-size: 12px; color: #999; margin-top: 5px;">Online Sale</div>
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">توقيع المدفوع</div>
            </div>
        </div>

        <!-- الفوتر -->
        <div class="receipt-footer">
            <div class="footer-info">
                <strong>Online Sale - أونلاين سيل</strong><br>
                📍 خانيونس - شمال مفترق النص - بجانب مجوهرات الترتوري<br>
                📞 059-784-8937 | 💬 +970592552702
            </div>
        </div>
    </div>

    <script>
        // دالة لتحويل الأرقام إلى كلمات عربية (بسيطة)
        function numberToArabicWords(number) {
            const ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
            const tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
            const teens = ['عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
            const hundreds = ['', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

            number = Math.floor(number);
            
            if (number === 0) return 'صفر';
            if (number >= 10000) return number.toString(); // للأرقام الكبيرة

            let result = '';

            // الآلاف
            const thousands = Math.floor(number / 1000);
            if (thousands > 0) {
                if (thousands === 1) result += 'ألف و';
                else if (thousands === 2) result += 'ألفان و';
                else result += ones[thousands] + ' آلاف و';
                number %= 1000;
            }

            // المئات
            const hundredsDigit = Math.floor(number / 100);
            if (hundredsDigit > 0) {
                result += hundreds[hundredsDigit] + ' و';
                number %= 100;
            }

            // العشرات والآحاد
            if (number >= 10 && number < 20) {
                result += teens[number - 10];
            } else {
                const tensDigit = Math.floor(number / 10);
                const onesDigit = number % 10;
                
                if (tensDigit > 0) {
                    result += tens[tensDigit];
                    if (onesDigit > 0) result += ' و';
                }
                if (onesDigit > 0) {
                    result += ones[onesDigit];
                }
            }

            return result.replace(/\sو$/, ''); // إزالة "و" الزائدة في النهاية
        }

        // تطبيق التحويل على الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const amountElement = document.querySelector('.amount-words');
            if (amountElement) {
                const amount = parseFloat('{{ $invoice->afterDiscount_amount }}');
                const words = numberToArabicWords(amount);
                amountElement.textContent = `فقط ${words} شيكل لا غير`;
            }
        });
    </script>
</body>
</html>