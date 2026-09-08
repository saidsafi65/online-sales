@extends('layout.app')

@section('title', 'طباعة باركود - ' . $item->product)

@section('content')
<div class="print-buttons text-center mb-4">
    <button onclick="window.print()" class="btn btn-success"><i class="fas fa-print"></i> طباعة</button>
    <a href="{{ route('catalog.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> رجوع</a>
</div>

@if (! $item->barcode)
    <div class="alert alert-warning text-center" style="max-width: 400px; margin: 0 auto;">
        هاد المنتج ما إله باركود بعد — روح لصفحة تعديله وولّد باركود أول.
        <br>
        <a href="{{ route('catalog.edit', $item->id) }}" class="btn btn-sm btn-primary mt-2">تعديل المنتج</a>
    </div>
@else
    <div class="label-wrap">
        <div class="label-card">
            <div class="label-product">{{ $item->product }}</div>
            @if ($item->type)
                <div class="label-type">{{ $item->type }}</div>
            @endif
            <svg id="barcodeSvg"></svg>
            <div class="label-price">{{ number_format((float) $item->sale_price, 2) }} شيكل</div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/JsBarcode/3.11.6/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcodeSvg", @json($item->barcode), {
            format: "CODE128",
            width: 2,
            height: 60,
            fontSize: 14,
            margin: 6,
        });
    </script>
@endif

<style>
    .label-wrap { display: flex; justify-content: center; padding: 1rem; }
    .label-card {
        width: 260px; border: 2px dashed #cbd5e1; border-radius: 10px; padding: 1rem;
        text-align: center; background: white;
    }
    .label-product { font-weight: 800; font-size: 1rem; color: #1e293b; margin-bottom: .15rem; }
    .label-type { font-size: .82rem; color: #64748b; margin-bottom: .5rem; }
    .label-price { font-weight: 800; font-size: 1.15rem; color: #10b981; margin-top: .4rem; }

    @media print {
        .print-buttons { display: none; }
        .label-card { border: none; }
        body { background: white; }
    }
</style>
@endsection
