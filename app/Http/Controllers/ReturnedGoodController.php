<?php

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
        $returnedGoods = ReturnedGood::latest()->paginate(15);

        $pendingCount = ReturnedGood::where('status', 'pending')->count();
        $returnedCount = ReturnedGood::where('status', 'returned')->count();
        $resolvedCount = ReturnedGood::whereIn('status', ['replaced', 'refunded'])->count();

        return view('returned-goods.index', compact(
            'returnedGoods',
            'pendingCount',
            'returnedCount',
            'resolvedCount'
        ));
    }

    public function create()
    {
        // جلب المنتجات التي الكمية لها أكبر من صفر
        $availableProducts = \App\Models\CatalogItem::where('quantity', '>', 0)->get();

        // تمريرها إلى الـ View
        return view('returned-goods.create', compact('availableProducts'));
    }

    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'reason' => 'required|string',
            'issue_discovered_date' => 'required|date',
            'status' => 'nullable|in:pending,returned,replaced,refunded',
            'notes' => 'nullable|string',
            'branch_id' => auth()->user()->branch_id,
        ]);

        // البحث عن المنتج في قاعدة البيانات
        $catalogItem = \App\Models\CatalogItem::where('product', $validated['product_name'])->first();

        if ($catalogItem) {
            // التحقق إذا كانت الكمية أكبر من صفر
            if ($catalogItem->quantity > 0) {
                // تقليص الكمية بمقدار 1 إذا كانت الكمية أكبر من 0
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

        // إنشاء سجل جديد للبضاعة المرجعة
        ReturnedGood::create($validated);

        // إعادة التوجيه مع رسالة النجاح
        return redirect()->route('returned-goods.index')
            ->with('success', 'تم إضافة البضاعة المرجعة بنجاح');
    }

    public function edit(ReturnedGood $returnedGood)
    {
        return view('returned-goods.edit', compact('returnedGood'));
    }

    public function update(Request $request, ReturnedGood $returnedGood)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'reason' => 'required|string',
            'issue_discovered_date' => 'required|date',
            'status' => 'required|in:pending,returned,replaced,refunded',
            'notes' => 'nullable|string',
        ]);

        $returnedGood->update($validated);

        return redirect()->route('returned-goods.index')
            ->with('success', 'تم تحديث البضاعة المرجعة بنجاح');
    }

    public function destroy(ReturnedGood $returnedGood)
    {
        $returnedGood->delete();

        return redirect()->route('returned-goods.index')
            ->with('success', 'تم حذف السجل بنجاح');
    }

    public function show(ReturnedGood $returnedGood)
    {
        return view('returned-goods.show', compact('returnedGood'));
    }
}
