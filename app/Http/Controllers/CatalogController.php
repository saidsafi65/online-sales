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
        $query->where('branch_id', auth()->user()->branch_id);
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

        // الترتيب
        $sortBy = $request->get('sort_by', 'product');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

    $items = $query->paginate(30)->withQueryString();

    // حساب إجمالي قيمة المخزون على مستوى قاعدة البيانات بالكامل
    $totals = CatalogItem::selectRaw('SUM(quantity * sale_price) as totalInventoryValue, SUM(quantity * wholesale_price) as totalWholesaleValue')->first();

    $totalInventoryValue = $totals->totalInventoryValue ? (float) $totals->totalInventoryValue : 0.0;
    $totalWholesaleValue = $totals->totalWholesaleValue ? (float) $totals->totalWholesaleValue : 0.0;

    return view('catalog.index', compact('items', 'totalInventoryValue', 'totalWholesaleValue'));
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
            'quantity' => 'required|integer|min:1',
            'wholesale_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // البحث عن نفس المنتج والنوع، إذا وجد، سيتم تحديث الكمية، وإذا لم يوجد سيتم إضافته
        CatalogItem::updateOrCreate(
            [
                'product' => $request->product,
                'type' => $request->type,
            ],
            [
                'quantity' => DB::raw('quantity + '.(int) $request->quantity), // جمع الكمية الحالية مع الكمية المدخلة
                'wholesale_price' => $request->wholesale_price,
                'sale_price' => $request->sale_price,
            ]
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
            'quantity' => 'required|integer|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $item = CatalogItem::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('catalog.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(CatalogItem $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('catalog.index')->with('success', 'تم الحذف من الكتالوج');
    }
}
