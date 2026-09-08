<?php
// ============================================================
// FILE: app/Http/Controllers/ReturnedGoodController.php
// FIX: branch_id كان داخل validate() كقيمة ثابتة وليس كقاعدة - نقله لخارجها
// ============================================================

namespace App\Http\Controllers;

use App\Models\ReturnedGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnedGoodController extends Controller
{
    public function index()
    {
        $query = ReturnedGood::query();

        if (!auth()->user()->isAdmin()) {
            \App\Support\BranchFilter::apply($query);
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

        try {
            DB::transaction(function () use ($validated) {
                // قفل الصف حتى ما يصير تعارض لو موظفين رجّعوا نفس المنتج بنفس اللحظة
                $catalogItem = \App\Models\CatalogItem::where('product', $validated['product_name'])
                    ->lockForUpdate()
                    ->first();

                if (! $catalogItem) {
                    throw new \RuntimeException('لم يتم العثور على المنتج في الكتالوج');
                }
                if ($catalogItem->quantity <= 0) {
                    throw new \RuntimeException('الكمية غير كافية لتخزين المرجع');
                }

                $catalogItem->decrement('quantity');

                ReturnedGood::create($validated);
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('returned-goods.index')->with('error', $e->getMessage());
        }

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
        // الوحدة اللي اتنقصت من الكتالوج لحظة إنشاء السجل لازم ترجع — الحذف هون معناه
        // "هالسجل ما إله داعي" مش "المنتج ضاع".
        DB::transaction(function () use ($returnedGood) {
            $catalogItem = \App\Models\CatalogItem::where('product', $returnedGood->product_name)
                ->lockForUpdate()
                ->first();

            if ($catalogItem) {
                $catalogItem->increment('quantity');
            }

            $returnedGood->delete();
        });

        return redirect()->route('returned-goods.index')->with('success', 'تم حذف السجل بنجاح وإرجاع الكمية للكتالوج');
    }

    public function show(ReturnedGood $returnedGood)
    {
        return view('returned-goods.show', compact('returnedGood'));
    }
}