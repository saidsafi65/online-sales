<?php
// ============================================================
// FILE: app/Http/Controllers/ReturnedGoodController.php
// FIX: branch_id كان داخل validate() كقيمة ثابتة وليس كقاعدة - نقله لخارجها
// ============================================================

namespace App\Http\Controllers;

use App\Models\ReturnedGood;
use Illuminate\Http\Request;

class ReturnedGoodController extends Controller
{
    public function index()
    {
        $query = ReturnedGood::query();

        if (!auth()->user()->isAdmin()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        // ✅ استخدام $query بدلاً من ReturnedGood::latest()
        $returnedGoods = $query->latest()->paginate(15);

        $pendingCount  = ReturnedGood::where('status', 'pending')->count();
        $returnedCount = ReturnedGood::where('status', 'returned')->count();
        $resolvedCount = ReturnedGood::whereIn('status', ['replaced', 'refunded'])->count();

        return view('returned-goods.index', compact(
            'returnedGoods', 'pendingCount', 'returnedCount', 'resolvedCount'
        ));
    }

    public function create()
    {
        $availableProducts = \App\Models\CatalogItem::where('quantity', '>', 0)->get();
        return view('returned-goods.create', compact('availableProducts'));
    }

    public function store(Request $request)
    {
        // ✅ branch_id خارج validate
        $validated = $request->validate([
            'supplier_name'        => 'required|string|max:255',
            'product_name'         => 'required|string|max:255',
            'reason'               => 'required|string',
            'issue_discovered_date'=> 'required|date',
            'status'               => 'nullable|in:pending,returned,replaced,refunded',
            'notes'                => 'nullable|string',
        ]);

        // ✅ إضافة branch_id بعد التحقق
        $validated['branch_id'] = auth()->user()->branch_id;

        $catalogItem = \App\Models\CatalogItem::where('product', $validated['product_name'])->first();

        if ($catalogItem) {
            if ($catalogItem->quantity > 0) {
                $catalogItem->quantity -= 1;
                $catalogItem->save();
            } else {
                return redirect()->route('returned-goods.index')
                    ->with('error', 'الكمية غير كافية لتخزين المرجع');
            }
        } else {
            return redirect()->route('returned-goods.index')
                ->with('error', 'لم يتم العثور على المنتج في الكتالوج');
        }

        ReturnedGood::create($validated);

        return redirect()->route('returned-goods.index')->with('success', 'تم إضافة البضاعة المرجعة بنجاح');
    }

    public function edit(ReturnedGood $returnedGood)
    {
        return view('returned-goods.edit', compact('returnedGood'));
    }

    public function update(Request $request, ReturnedGood $returnedGood)
    {
        $validated = $request->validate([
            'supplier_name'        => 'required|string|max:255',
            'product_name'         => 'required|string|max:255',
            'reason'               => 'required|string',
            'issue_discovered_date'=> 'required|date',
            'status'               => 'required|in:pending,returned,replaced,refunded',
            'notes'                => 'nullable|string',
        ]);

        $returnedGood->update($validated);

        return redirect()->route('returned-goods.index')->with('success', 'تم تحديث البضاعة المرجعة بنجاح');
    }

    public function destroy(ReturnedGood $returnedGood)
    {
        $returnedGood->delete();
        return redirect()->route('returned-goods.index')->with('success', 'تم حذف السجل بنجاح');
    }

    public function show(ReturnedGood $returnedGood)
    {
        return view('returned-goods.show', compact('returnedGood'));
    }
}