<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PurchasesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Purchase::query();

        // إذا كان المستخدم ليس مدير نظام، اعرض فقط مشتريات فرعه
        if (!auth()->user()->isAdmin()) {
             $query->where('branch_id', auth()->user()->branch_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('is_returned')) {
            $query->where('is_returned', (bool) $request->is_returned);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $purchases = $query->orderByDesc('purchase_date')
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        return view('purchases.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'item' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,app,mixed',
            'amount_cash' => 'required|numeric|min:0',
            'amount_bank' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'id_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_returned' => 'nullable|boolean',
            'issue' => 'nullable|string',
            'return_date' => 'nullable|date',
            'branch_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $idImagePath = null;
            if ($request->hasFile('id_image')) {
                $file = $request->file('id_image');
                $destinationPath = public_path('uploads/purchases');
                if (! is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $filename = Str::random(20).'.'.$file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                $idImagePath = 'uploads/purchases/'.$filename; // relative to public
            }

            Purchase::create([
                'item' => $request->item,
                'type' => $request->type,
                'quantity' => (int) $request->quantity,
                'payment_method' => $request->payment_method,
                'amount_cash' => $request->amount_cash,
                'amount_bank' => $request->amount_bank,
                'purchase_date' => $request->purchase_date,
                'supplier_name' => $request->supplier_name,
                'phone' => $request->phone,
                'id_image' => $idImagePath,
                'is_returned' => (bool) $request->is_returned,
                'issue' => $request->issue,
                'return_date' => $request->return_date,
                'notes' => $request->notes,
                'branch_id' => auth()->user()->branch_id, // assign branch on create
            ]);

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'تم إضافة عملية الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الشراء: '.$e->getMessage())->withInput();
        }
    }

    public function edit(Purchase $purchase): View
    {
        return view('purchases.edit', compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $rules = [
            'item' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,app,mixed',
            'amount_cash' => 'required|numeric|min:0',
            'amount_bank' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'id_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_returned' => 'nullable|boolean',
            'issue' => 'nullable|string',
            'return_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $idImagePath = $purchase->id_image;
            if ($request->hasFile('id_image')) {
                $file = $request->file('id_image');
                $destinationPath = public_path('uploads/purchases');
                if (! is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $filename = Str::random(20).'.'.$file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                $idImagePath = 'uploads/purchases/'.$filename;
            }

            $becomingReturned = ! $purchase->is_returned && $request->boolean('is_returned');

            $amountCash = (float) $request->amount_cash;
            $amountBank = (float) $request->amount_bank;
            $notes = $request->notes;

            if ($becomingReturned) {
                // رجّعنا البضاعة للمورد واسترجعنا فلوسنا — لازم هالمصروف ما يضل محسوب،
                // والكمية يلي دخلت الكتالوج وقت الشراء لازم تطلع منه (رجعت للمورد فعليًا).
                $notes = ($notes ? $notes.' - ' : '')
                    .'تم إرجاع الشراء في '.now()->format('Y-m-d H:i:s')
                    ." (مبلغ مسترجع: نقدي {$amountCash} + بنكي {$amountBank})";

                $catalogItem = \App\Models\CatalogItem::where('product', $request->item)
                    ->where('type', $request->type)
                    ->lockForUpdate()
                    ->first();
                if ($catalogItem) {
                    $catalogItem->decrement('quantity', min((int) $request->quantity, (int) $catalogItem->quantity));
                }

                $amountCash = 0;
                $amountBank = 0;
            }

            $purchase->update([
                'item' => $request->item,
                'type' => $request->type,
                'quantity' => (int) $request->quantity,
                'payment_method' => $request->payment_method,
                'amount_cash' => $amountCash,
                'amount_bank' => $amountBank,
                'purchase_date' => $request->purchase_date,
                'supplier_name' => $request->supplier_name,
                'phone' => $request->phone,
                'id_image' => $idImagePath,
                'is_returned' => (bool) $request->is_returned,
                'issue' => $request->issue,
                'return_date' => $request->return_date,
                'notes' => $notes,
            ]);

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'تم تحديث عملية الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الشراء: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'تم حذف عملية الشراء');
    }

    public function createCatalog(): View
    {
        return view('purchases.create_catalog');
    }

    public function storeCatalog(Request $request): RedirectResponse
    {
        $rules = [
            'item' => 'required|string|max:120',
            'type' => 'required|string|max:120',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,app,mixed',
            'amount_cash' => 'required|numeric|min:0',
            'amount_bank' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'id_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_returned' => 'nullable|boolean',
            'issue' => 'nullable|string',
            'return_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // معالجة رفع صورة الهوية
            $idImagePath = null;
            if ($request->hasFile('id_image')) {
                $file = $request->file('id_image');
                $destinationPath = public_path('uploads/purchases');
                if (! is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $filename = Str::random(20).'.'.$file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                $idImagePath = 'uploads/purchases/'.$filename;
            }

            // 1. إضافة السجل في جدول المشتريات
            $purchase = Purchase::create([
                'item' => $request->item,
                'type' => $request->type,
                'quantity' => (int) $request->quantity,
                'payment_method' => $request->payment_method,
                'amount_cash' => $request->amount_cash,
                'amount_bank' => $request->amount_bank,
                'purchase_date' => $request->purchase_date,
                'supplier_name' => $request->supplier_name,
                'phone' => $request->phone,
                'id_image' => $idImagePath,
                'is_returned' => (bool) $request->is_returned,
                'issue' => $request->issue,
                'return_date' => $request->return_date,
                'notes' => $request->notes,
            ]);

            // 2. إضافة أو تحديث الكتالوج — عن طريق موديل CatalogItem (مش DB::table الخام) حتى
            // يشتغل التزامن التلقائي مع المنتجات المرتبطة (CatalogItem::syncProductStock)؛
            // التحديث عن طريق DB::table كان يتجاوز هالمزامنة بالكامل.
            $catalogQuery = \App\Models\CatalogItem::where('product', $request->item)
                ->where('type', $request->type);
            if (!auth()->user()->isAdmin()) {
                $catalogQuery->where('branch_id', auth()->user()->branch_id);
            }
            $catalogItem = $catalogQuery->lockForUpdate()->first();

            if ($catalogItem) {
                $catalogItem->wholesale_price = (string) $request->wholesale_price;
                $catalogItem->sale_price = (string) $request->sale_price;
                $catalogItem->save();
                $catalogItem->increment('quantity', (int) $request->quantity);
            } else {
                $catalogItem = \App\Models\CatalogItem::create([
                    'product' => $request->item,
                    'type' => $request->type,
                    'quantity' => (string) $request->quantity,
                    'wholesale_price' => (string) $request->wholesale_price,
                    'sale_price' => (string) $request->sale_price,
                    'branch_id' => auth()->user()->isAdmin() ? null : auth()->user()->branch_id,
                ]);
            }

            DB::commit();

            // تنبيه غير ملزم لو المبلغ المدفوع فعليًا مختلف عن التكلفة المتوقعة (سعر الجملة × الكمية)
            $priceWarning = null;
            $expectedCost = (float) $request->wholesale_price * (int) $request->quantity;
            $paidTotal = (float) $request->amount_cash + (float) $request->amount_bank;
            if ($expectedCost > 0 && round($paidTotal, 2) !== round($expectedCost, 2)) {
                $priceWarning = 'تنبيه: المبلغ المدفوع ('.number_format($paidTotal, 2).') مختلف عن التكلفة المتوقعة حسب سعر الجملة ('.number_format($expectedCost, 2).'). تأكد إنه مقصود.';
            }

            return redirect()->route('purchases.index')
                ->with('success', 'تم إضافة عملية الشراء بنجاح وتحديث الكتالوج')
                ->with('warning', $priceWarning);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الحفظ: '.$e->getMessage())
                ->withInput();
        }
    }
}
