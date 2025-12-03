<?php

namespace App\Http\Controllers;

use App\Models\MobileMaintenance;
use App\Models\MobileSale;
use App\Models\MobileInventory;
use App\Models\MobileDebt;
use App\Models\MobileExpense;
use App\Models\CatalogItem;
use App\Models\Repair;
use App\Models\Sale;
use App\Models\Debt;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class MobileShopController extends Controller
{
    // ===== المبيعات =====
    public function salesIndex()
    {
        if (!Schema::hasTable('mobile_sales')) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $sales = new LengthAwarePaginator([], 0, 15, $currentPage);
            return view('mobile-shop.sales.index', compact('sales'));
        }

        $sales = MobileSale::where('branch_id', auth()->user()->branch_id ?? null)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('mobile-shop.sales.index', compact('sales'));
    }

    public function salesCreate()
    {
        // جلب المنتجات من المخزن
        $inventoryQuery = MobileInventory::where('branch_id', auth()->user()->branch_id ?? null)
            ->where('quantity', '>', 0)
            ->orderBy('product_name')
            ->orderBy('model_type');
        
        $inventory = $inventoryQuery->get();
        
        // تجميع المنتجات حسب الاسم والأنواع
        $products = $inventory->groupBy('product_name')
            ->map(function ($group) {
                return $group->pluck('model_type')->unique()->values();
            })->filter(function ($types) {
                return $types->isNotEmpty();
            });

        return view('mobile-shop.sales.create', compact('products', 'inventory'));
    }

    public function salesStore(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:نقدي,تطبيق,مختلط',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'created_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $requestedQty = $validated['quantity'];
            $branchId = auth()->user()->branch_id;

            // 1. التحقق من الكمية في مخزون معرض الجوال
            $mobileInventoryItem = MobileInventory::where('product_name', $validated['product_name'])
                ->where('model_type', $validated['product_type'])
                ->where('branch_id', $branchId)
                ->first();

            if (!$mobileInventoryItem || $mobileInventoryItem->quantity < $requestedQty) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في مخزون معرض الجوال'])
                    ->withInput();
            }

            // 2. التحقق من الكمية في الكتالوج الرئيسي
            $catalogItem = CatalogItem::where('product', $validated['product_name'])
                ->where('type', $validated['product_type'])
                ->where('branch_id', $branchId)
                ->first();

            if (!$catalogItem || $catalogItem->quantity < $requestedQty) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في الكتالوج الرئيسي'])
                    ->withInput();
            }

            // 3. خصم الكمية من مخزون معرض الجوال
            $mobileInventoryItem->decrement('quantity', $requestedQty);

            // 4. خصم الكمية من الكتالوج الرئيسي
            $catalogItem->decrement('quantity', $requestedQty);

            $validated['branch_id'] = $branchId;
            $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            
            // 5. إنشاء في جدول مبيعات معرض الجوال
            // طبّق created_at إن أُرسل
            $mobileSaleData = $validated;
            if (!empty($validated['created_at'])) {
                $mobileSaleData['created_at'] = $validated['created_at'];
            }
            MobileSale::create($mobileSaleData);

            // 6. إضافة تلقائياً في جدول المبيعات الرئيسي
            Sale::create([
                'product' => $validated['product_name'],
                'type' => $validated['product_type'],
                'quantity' => $requestedQty,
                'sale_date' => now(),
                'payment_method' => match($validated['payment_method']) {
                    'نقدي' => 'cash',
                    'تطبيق' => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'cash_amount' => $validated['cash_amount'],
                'app_amount' => $validated['bank_amount'],
                'branch_id' => $branchId,
                'is_returned' => false,
                'notes' => '✅ مبيعة من معرض الجوال',
                'created_at' => $validated['created_at'] ?? now(),
            ]);

            DB::commit();
            return redirect()->route('mobile-shop.sales.index')
                ->with('success', 'تم إضافة المبيعة بنجاح في معرض الجوال والمبيعات الرئيسية وخصم الكمية من المخزونين');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function salesEdit(MobileSale $sale)
    {
        // جلب المنتجات من المخزن
        $inventoryQuery = MobileInventory::where('branch_id', auth()->user()->branch_id ?? null)
            ->where('quantity', '>', 0)
            ->orderBy('product_name')
            ->orderBy('model_type');
        
        $inventory = $inventoryQuery->get();
        
        $products = $inventory->groupBy('product_name')
            ->map(function ($group) {
                return $group->pluck('model_type')->unique()->values();
            })->filter(function ($types) {
                return $types->isNotEmpty();
            });

        return view('mobile-shop.sales.edit', compact('sale', 'products', 'inventory'));
    }

    public function salesUpdate(Request $request, MobileSale $sale)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:نقدي,تطبيق,مختلط',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'created_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $oldQuantity = $sale->quantity;
            $newQuantity = $validated['quantity'];
            $branchId = auth()->user()->branch_id;
            
            // إذا تغير المنتج أو النوع
            if ($sale->product_name !== $validated['product_name'] || 
                $sale->product_type !== $validated['product_type']) {
                
                // 1. إعادة الكمية القديمة لمخزون معرض الجوال
                $oldMobileInventory = MobileInventory::where('product_name', $sale->product_name)
                    ->where('model_type', $sale->product_type)
                    ->where('branch_id', $branchId)
                    ->first();
                
                if ($oldMobileInventory) {
                    $oldMobileInventory->increment('quantity', $oldQuantity);
                }

                // 2. إعادة الكمية القديمة للكتالوج الرئيسي
                $oldCatalog = CatalogItem::where('product', $sale->product_name)
                    ->where('type', $sale->product_type)
                    ->where('branch_id', $branchId)
                    ->first();
                
                if ($oldCatalog) {
                    $oldCatalog->increment('quantity', $oldQuantity);
                }
                
                // 3. خصم الكمية الجديدة من مخزون معرض الجوال
                $newMobileInventory = MobileInventory::where('product_name', $validated['product_name'])
                    ->where('model_type', $validated['product_type'])
                    ->where('branch_id', $branchId)
                    ->first();
                
                if (!$newMobileInventory || $newMobileInventory->quantity < $newQuantity) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في مخزون معرض الجوال'])
                        ->withInput();
                }
                
                $newMobileInventory->decrement('quantity', $newQuantity);

                // 4. خصم الكمية الجديدة من الكتالوج الرئيسي
                $newCatalog = CatalogItem::where('product', $validated['product_name'])
                    ->where('type', $validated['product_type'])
                    ->where('branch_id', $branchId)
                    ->first();
                
                if (!$newCatalog || $newCatalog->quantity < $newQuantity) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في الكتالوج الرئيسي'])
                        ->withInput();
                }
                
                $newCatalog->decrement('quantity', $newQuantity);
            } 
            // إذا تغيرت الكمية فقط
            else if ($oldQuantity !== $newQuantity) {
                $delta = $newQuantity - $oldQuantity;
                
                // 1. تحديث مخزون معرض الجوال
                $mobileInventoryItem = MobileInventory::where('product_name', $validated['product_name'])
                    ->where('model_type', $validated['product_type'])
                    ->where('branch_id', $branchId)
                    ->first();
                
                if (!$mobileInventoryItem) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'المنتج غير موجود في مخزون معرض الجوال'])
                        ->withInput();
                }

                // 2. تحديث الكتالوج الرئيسي
                $catalogItem = CatalogItem::where('product', $validated['product_name'])
                    ->where('type', $validated['product_type'])
                    ->where('branch_id', $branchId)
                    ->first();
                
                if (!$catalogItem) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'المنتج غير موجود في الكتالوج الرئيسي'])
                        ->withInput();
                }
                
                if ($delta > 0) {
                    // زيادة الكمية - يجب التحقق من التوفر
                    if ($mobileInventoryItem->quantity < $delta) {
                        return redirect()->back()
                            ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في مخزون معرض الجوال'])
                            ->withInput();
                    }
                    if ($catalogItem->quantity < $delta) {
                        return redirect()->back()
                            ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في الكتالوج الرئيسي'])
                            ->withInput();
                    }
                    $mobileInventoryItem->decrement('quantity', $delta);
                    $catalogItem->decrement('quantity', $delta);
                } else {
                    // تقليل الكمية - إعادة للمخزون
                    $mobileInventoryItem->increment('quantity', -$delta);
                    $catalogItem->increment('quantity', -$delta);
                }
            }

            $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            // تمرير created_at عند التحديث إذا تم إدخاله
            $updateData = $validated;
            if (!empty($validated['created_at'])) {
                $updateData['created_at'] = $validated['created_at'];
            }
            $sale->update($updateData);
            
            DB::commit();
            return redirect()->route('mobile-shop.sales.index')
                ->with('success', 'تم تحديث المبيعة بنجاح وتحديث المخزون في الطرفين');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function salesDestroy(MobileSale $sale)
    {
        DB::beginTransaction();
        try {
            $branchId = auth()->user()->branch_id;
            
            // 1. إعادة الكمية لمخزون معرض الجوال
            $mobileInventoryItem = MobileInventory::where('product_name', $sale->product_name)
                ->where('model_type', $sale->product_type)
                ->where('branch_id', $branchId)
                ->first();
            
            if ($mobileInventoryItem) {
                $mobileInventoryItem->increment('quantity', $sale->quantity);
            }

            // 2. إعادة الكمية للكتالوج الرئيسي
            $catalogItem = CatalogItem::where('product', $sale->product_name)
                ->where('type', $sale->product_type)
                ->where('branch_id', $branchId)
                ->first();
            
            if ($catalogItem) {
                $catalogItem->increment('quantity', $sale->quantity);
            }
            
            // 3. حذف المبيعة
            $sale->delete();
            
            DB::commit();
            return redirect()->route('mobile-shop.sales.index')
                ->with('success', 'تم حذف المبيعة بنجاح وإعادة الكمية للمخزون في الطرفين');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ===== الصيانة =====
    public function maintenanceIndex()
    {
        if (! Schema::hasTable('mobile_maintenance')) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $maintenances = new LengthAwarePaginator([], 0, 15, $currentPage);
            return view('mobile-shop.maintenance.index', compact('maintenances'));
        }

        $maintenances = MobileMaintenance::where('branch_id', auth()->user()->branch_id ?? null)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('mobile-shop.maintenance.index', compact('maintenances'));
    }

    public function maintenanceCreate()
    {
        return view('mobile-shop.maintenance.create');
    }

    public function maintenanceStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'problem_description' => 'required|string',
            'mobile_type' => 'required|string|max:255',
            'payment_method' => 'required|in:نقدي,تطبيق,مختلط',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'delivery_date' => 'nullable|date',
            'receipt_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['branch_id'] = auth()->user()->branch_id;
            $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);

            $mobileMaintenance = MobileMaintenance::create($validated);

            Repair::create([
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone_number'],
                'device_name' => 'جوال - ' . $validated['mobile_type'],
                'model' => $validated['mobile_type'],
                'issue' => $validated['problem_description'],
                'received_date' => now(),
                'delivery_date' => $validated['delivery_date'] ?? null,
                'cost_cash' => $validated['cash_amount'],
                'cost_bank' => $validated['bank_amount'],
                'payment_method' => match($validated['payment_method']) {
                    'نقدي' => 'cash',
                    'تطبيق' => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'received_by' => auth()->user()->name,
                'branch_id' => auth()->user()->branch_id,
                'is_returned' => false,
                'notes' => 'تم إضافتها من معرض الجوال'
            ]);

            DB::commit();
            return redirect()->route('mobile-shop.maintenance.index')->with('success', 'تم إضافة الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ===== المخزون =====
    public function inventoryCreate()
    {
        return view('mobile-shop.inventory.create');
    }

    public function inventoryStore(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'model_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $validated['branch_id'] = auth()->user()->branch_id;

            MobileInventory::create($validated);

            CatalogItem::updateOrCreate(
                [
                    'product' => $validated['product_name'],
                    'type' => $validated['model_type'],
                    'branch_id' => auth()->user()->branch_id
                ],
                [
                    'quantity' => DB::raw('COALESCE(quantity, 0) + ' . (int)$validated['quantity']),
                    'wholesale_price' => $validated['wholesale_price'],
                    'sale_price' => $validated['selling_price'],
                    'is_mobile_product' => true
                ]
            );

            DB::commit();
            return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم إضافة المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ===== الديون =====
    public function debtsCreate()
    {
        return view('mobile-shop.debts.create');
    }

    public function debtsStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'type' => 'required|string|max:255',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'debt_date' => 'required|date',
            'payment_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['total'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            $validated['branch_id'] = auth()->user()->branch_id;

            MobileDebt::create($validated);

            Debt::create([
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone_number'],
                'type' => 'مدين',
                'cash_amount' => $validated['cash_amount'],
                'bank_amount' => $validated['bank_amount'],
                'reason' => 'دين من معرض الجوال - ' . $validated['type'],
                'debt_date' => $validated['debt_date'],
                'payment_date' => $validated['payment_date'] ?? null,
                'branch_id' => auth()->user()->branch_id,
            ]);

            DB::commit();
            return redirect()->route('mobile-shop.debts.index')->with('success', 'تم إضافة الدين بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ===== المصروفات =====
    public function expensesIndex()
    {
        if (! Schema::hasTable('mobile_expenses')) {
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $expenses = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, $currentPage);
            return view('mobile-shop.expenses.index', compact('expenses'));
        }

        $expenses = MobileExpense::where('branch_id', auth()->user()->branch_id ?? null)
            ->orderBy('expense_date', 'desc')
            ->paginate(15);
        return view('mobile-shop.expenses.index', compact('expenses'));
    }

    public function expensesCreate()
    {
        return view('mobile-shop.expenses.create');
    }

    public function expensesStore(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:نقدي,بنكي,مختلط',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'id_photo' => 'nullable|file',
            'reference' => 'nullable|string|max:255',
            'defect' => 'nullable|string|max:255',
            'return_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['total'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            $validated['branch_id'] = auth()->user()->branch_id;

            if ($request->hasFile('id_photo')) {
                $file = $request->file('id_photo');
                $destinationPath = public_path('uploads/mobile-expenses');
                if (! is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $file->move($destinationPath, $filename);
                $validated['id_photo'] = 'uploads/mobile-expenses/' . $filename;
            }

            MobileExpense::create($validated);

            Purchase::create([
                'item' => $validated['category'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'payment_method' => match($validated['payment_method']) {
                    'نقدي' => 'cash',
                    'بنكي' => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'amount_cash' => $validated['cash_amount'],
                'amount_bank' => $validated['bank_amount'],
                'purchase_date' => $validated['expense_date'],
                'supplier_name' => $validated['supplier_name'] ?? null,
                'phone' => $validated['supplier_phone'] ?? null,
                'id_image' => $validated['id_photo'] ?? null,
                'notes' => 'تم إضافتها من معرض الجوال - ' . ($validated['notes'] ?? ''),
                'branch_id' => auth()->user()->branch_id,
                'is_returned' => false,
            ]);

            DB::commit();
            return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم إضافة المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // باقي الدوال (maintenanceIndex, inventoryIndex, etc.) تبقى كما هي...
    // يمكنك نسخها من الكود الأصلي
    public function inventoryIndex()
    {
        $branchId = auth()->user()->branch_id;
        
        // جلب بيانات المخزون بناءً على الفرع
        $inventory = MobileInventory::where('branch_id', $branchId)
            ->orderBy('product_name')
            ->orderBy('model_type')
            ->paginate(15);

        return view('mobile-shop.inventory.index', compact('inventory'));
    }

    public function inventoryEdit(MobileInventory $inventory)
    {
        return view('mobile-shop.inventory.edit', compact('inventory'));
    }

    public function inventoryUpdate(Request $request, MobileInventory $inventory)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'model_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $branchId = auth()->user()->branch_id;

            $oldQuantity = (int) $inventory->quantity;
            $newQuantity = (int) $validated['quantity'];
            $delta = $newQuantity - $oldQuantity; // positive -> increase catalog, negative -> decrease

            // Update catalog item quantity accordingly
            $catalogItem = CatalogItem::firstOrCreate([
                'product' => $validated['product_name'],
                'type' => $validated['model_type'],
                'branch_id' => $branchId
            ], [
                'quantity' => 0,
                'wholesale_price' => $validated['wholesale_price'],
                'sale_price' => $validated['selling_price'],
                'is_mobile_product' => true
            ]);

            if ($delta !== 0) {
                if ($delta > 0) {
                    $catalogItem->increment('quantity', $delta);
                } else {
                    $toDec = (int) (-$delta);
                    $catalogItem->update(['quantity' => max(0, $catalogItem->quantity - $toDec)]);
                }
            }

            // Update prices
            $catalogItem->update([
                'wholesale_price' => $validated['wholesale_price'],
                'sale_price' => $validated['selling_price'],
            ]);

            // Update mobile inventory record
            $inventory->update($validated + ['branch_id' => $branchId]);

            DB::commit();
            return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم تحديث المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function inventoryDestroy(MobileInventory $inventory)
    {
        DB::beginTransaction();
        try {
            $branchId = auth()->user()->branch_id;
            $qty = (int) $inventory->quantity;

            // Adjust catalog item quantity
            $catalogItem = CatalogItem::where('product', $inventory->product_name)
                ->where('type', $inventory->model_type)
                ->where('branch_id', $branchId)
                ->first();

            if ($catalogItem) {
                $newQty = max(0, (int)$catalogItem->quantity - $qty);
                $catalogItem->update(['quantity' => $newQty]);
            }

            $inventory->delete();

            DB::commit();
            return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم حذف المنتج وإصلاح المخزون');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $branchId = auth()->user()->branch_id;
        
        $maintenanceCount = Schema::hasTable('mobile_maintenance')
            ? MobileMaintenance::where('branch_id', $branchId)->count()
            : 0;

        $salesCount = Schema::hasTable('mobile_sales')
            ? MobileSale::where('branch_id', $branchId)->count()
            : 0;

        $inventoryCount = Schema::hasTable('mobile_inventory')
            ? MobileInventory::where('branch_id', $branchId)->sum('quantity')
            : 0;

        $debtsCount = Schema::hasTable('mobile_debts')
            ? MobileDebt::where('branch_id', $branchId)->where('payment_date', null)->count()
            : 0;

        $expensesCount = Schema::hasTable('mobile_expenses')
            ? MobileExpense::where('branch_id', $branchId)->count()
            : 0;

        $totalMaintenance = Schema::hasTable('mobile_maintenance')
            ? MobileMaintenance::where('branch_id', $branchId)->sum('cost')
            : 0;

        $totalSales = Schema::hasTable('mobile_sales')
            ? MobileSale::where('branch_id', $branchId)->sum('cost')
            : 0;

        $totalDebts = Schema::hasTable('mobile_debts')
            ? MobileDebt::where('branch_id', $branchId)->where('payment_date', null)->sum('total')
            : 0;

        $totalExpenses = Schema::hasTable('mobile_expenses')
            ? MobileExpense::where('branch_id', $branchId)->sum('total')
            : 0;

        return view('mobile-shop.index', compact(
            'maintenanceCount',
            'salesCount',
            'inventoryCount',
            'debtsCount',
            'expensesCount',
            'totalMaintenance',
            'totalSales',
            'totalDebts',
            'totalExpenses'
        ));
    }
    
}