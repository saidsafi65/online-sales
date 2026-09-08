<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = CatalogItem::query();
        
        if (!auth()->user()->isAdmin()) {
            \App\Support\BranchFilter::apply($query);
        }
        
        // البحث بالاسم أو النوع
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // تصفية حسب الكمية
        if ($request->filled('quantity_filter')) {
            switch ($request->quantity_filter) {
                case 'low':
                    $query->where('quantity', '<=', 5);
                    break;
                case 'medium':
                    $query->whereBetween('quantity', [6, 20]);
                    break;
                case 'high':
                    $query->where('quantity', '>', 20);
                    break;
            }
        }

        // تصفية حسب نطاق السعر
        if ($request->filled('price_min')) {
            $query->where('sale_price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('sale_price', '<=', $request->price_max);
        }
        
        // تصفية حسب نوع المنتج (جوال أو عادي)
        if ($request->filled('product_source')) {
            if ($request->product_source === 'mobile') {
                $query->where('is_mobile_product', true);
            } elseif ($request->product_source === 'regular') {
                $query->where('is_mobile_product', false);
            }
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'product');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $items = $query->paginate(30)->withQueryString();

        // حساب إجمالي قيمة المخزون
        $totals = CatalogItem::selectRaw('SUM(quantity * sale_price) as totalInventoryValue, SUM(quantity * wholesale_price) as totalWholesaleValue')->first();

        $totalInventoryValue = $totals->totalInventoryValue ? (float) $totals->totalInventoryValue : 0.0;
        $totalWholesaleValue = $totals->totalWholesaleValue ? (float) $totals->totalWholesaleValue : 0.0;

        $totalOutOfStock = $query->where('quantity', '=', 0)->count();

        return view('catalog.index', compact('items', 'totalInventoryValue', 'totalWholesaleValue','totalOutOfStock'));
    }

    public function create(): View
    {
        return view('catalog.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required|string|max:120',
            'type' => 'required|string|max:120',
            'barcode' => 'nullable|string|max:64|unique:catalog_items,barcode',
            'quantity' => 'required|integer|min:1',
            'wholesale_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ], [
            'barcode.unique' => 'هذا الباركود مستخدم أصلاً لمنتج آخر',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $updateData = [
            'quantity' => DB::raw('quantity + '.(int) $request->quantity),
            'wholesale_price' => $request->wholesale_price,
            'sale_price' => $request->sale_price,
            'is_mobile_product' => false, // المنتجات المضافة يدوياً ليست من معرض الجوال
        ];
        if ($request->filled('barcode')) {
            $updateData['barcode'] = $request->barcode;
        }

        CatalogItem::updateOrCreate(
            [
                'product' => $request->product,
                'type' => $request->type,
            ],
            $updateData
        );

        return redirect()->route('catalog.index')->with('success', 'تمت الإضافة أو التحديث بنجاح');
    }

    public function edit($id): View
    {
        $item = CatalogItem::findOrFail($id);
        return view('catalog.edit', compact('item'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required|string|max:120',
            'type' => 'required|string|max:120',
            'barcode' => 'nullable|string|max:64|unique:catalog_items,barcode,' . $id,
            'quantity' => 'required|integer|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ], [
            'barcode.unique' => 'هذا الباركود مستخدم أصلاً لمنتج آخر',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $item = CatalogItem::findOrFail($id);
        $data = $request->all();
        // فاضي يعني "امسح الباركود" — لازم null صراحة، مش '' (فراغين بيتعارضوا مع unique
        // constraint، بعكس NULL يلي MySQL بيسمح بتكراره)
        $data['barcode'] = $request->filled('barcode') ? $request->barcode : null;
        $item->update($data);

        return redirect()->route('catalog.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    // توليد رقم باركود عشوائي غير مكرر — للاستخدام لما التاجر ما عنده باركود جاهز للمنتج
    public function generateBarcode(): \Illuminate\Http\JsonResponse
    {
        do {
            $code = (string) random_int(1000000000, 9999999999);
        } while (CatalogItem::where('barcode', $code)->exists());

        return response()->json(['barcode' => $code]);
    }

    // صفحة طباعة ملصق الباركود (اسم المنتج + السعر + الباركود نفسه)
    public function barcodeLabel(CatalogItem $item): View
    {
        return view('catalog.barcode-label', compact('item'));
    }

    public function destroy(CatalogItem $item): RedirectResponse
    {
        $item->delete();
        return redirect()->route('catalog.index')->with('success', 'تم الحذف من الكتالوج');
    }
}