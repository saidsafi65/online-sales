<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

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

        CatalogItem::firstOrCreate([
            'product' => $request->product,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'wholesale_price' => $request->wholesale_price,
            'sale_price' => $request->sale_price,
        ]);

        return redirect()->route('catalog.index')->with('success', 'تمت الإضافة إلى الكتالوج');
    }

    public function destroy(CatalogItem $item): RedirectResponse
    {
        $item->delete();
        return redirect()->route('catalog.index')->with('success', 'تم الحذف من الكتالوج');
    }
}
