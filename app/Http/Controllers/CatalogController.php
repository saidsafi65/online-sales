<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $items = CatalogItem::orderBy('product')->orderBy('type')->paginate(30);

        return view('catalog.index', compact('items'));
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
                'quantity' => \DB::raw('quantity + '.(int) $request->quantity), // جمع الكمية الحالية مع الكمية المدخلة
                'wholesale_price' => $request->wholesale_price,
                'sale_price' => $request->sale_price,
            ]
        );

        return redirect()->route('catalog.index')->with('success', 'تمت الإضافة أو التحديث بنجاح');
    }

    public function destroy(CatalogItem $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('catalog.index')->with('success', 'تم الحذف من الكتالوج');
    }
}
