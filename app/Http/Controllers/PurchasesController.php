<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            'id_image' => 'nullable|file',
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
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
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
            'id_image' => 'nullable|file',
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
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $file->move($destinationPath, $filename);
                $idImagePath = 'uploads/purchases/'.$filename;
            }

            $purchase->update([
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
            'id_image' => 'nullable|file',
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
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
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

            // 2. إضافة أو تحديث الكتالوج
            $catalogQuery = DB::table('catalog_items')
                ->where('product', $request->item)
                ->where('type', $request->type);
            if (!auth()->user()->isAdmin()) {
                $catalogQuery->where('branch_id', auth()->user()->branch_id);
            }
            $catalogItem = $catalogQuery->first();

            if ($catalogItem) {
                // المنتج موجود: تحديث الكمية والأسعار
                $newQuantity = (int) $catalogItem->quantity + (int) $request->quantity;
                $updateQuery = DB::table('catalog_items')
                    ->where('product', $request->item)
                    ->where('type', $request->type);
                if (!auth()->user()->isAdmin()) {
                    $updateQuery->where('branch_id', auth()->user()->branch_id);
                }
                $updateQuery->update([
                    'quantity' => (string) $newQuantity,
                    'wholesale_price' => (string) $request->wholesale_price,
                    'sale_price' => (string) $request->sale_price,
                    'updated_at' => now(),
                ]);
            } else {
                // المنتج غير موجود: إنشاء سجل جديد
                $insertData = [
                    'product' => $request->item,
                    'type' => $request->type,
                    'quantity' => (string) $request->quantity,
                    'wholesale_price' => (string) $request->wholesale_price,
                    'sale_price' => (string) $request->sale_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (!auth()->user()->isAdmin()) {
                    $insertData['branch_id'] = auth()->user()->branch_id;
                }
                DB::table('catalog_items')->insert($insertData);
            }

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'تم إضافة عملية الشراء بنجاح وتحديث الكتالوج');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الحفظ: '.$e->getMessage())
                ->withInput();
        }
    }
}
