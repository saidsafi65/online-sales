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
use Illuminate\Support\Str;
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

        return view('mobile-shop.sales.create', compact('products', 'inventory'));
    }

    public function salesStore(Request $request)
    {
        $validated = $request->validate([
            'product_name'   => 'required|string|max:255',
            'product_type'   => 'required|string|max:255',
            'quantity'       => 'required|integer|min:1',
            'payment_method' => 'required|in:نقدي,تطبيق,مختلط',
            'cash_amount'    => 'required|numeric|min:0',
            'bank_amount'    => 'required|numeric|min:0',
            'created_at'     => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $requestedQty = $validated['quantity'];
            $branchId = auth()->user()->branch_id;

            $mobileInventoryItem = MobileInventory::where('product_name', $validated['product_name'])
                ->where('model_type', $validated['product_type'])
                ->where('branch_id', $branchId)
                ->lockForUpdate()
                ->first();

            if (!$mobileInventoryItem || $mobileInventoryItem->quantity < $requestedQty) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في مخزون معرض الجوال'])
                    ->withInput();
            }

            $catalogItem = CatalogItem::where('product', $validated['product_name'])
                ->where('type', $validated['product_type'])
                ->where('branch_id', $branchId)
                ->lockForUpdate()
                ->first();

            if (!$catalogItem || $catalogItem->quantity < $requestedQty) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في الكتالوج الرئيسي'])
                    ->withInput();
            }

            $mobileInventoryItem->decrement('quantity', $requestedQty);
            $catalogItem->decrement('quantity', $requestedQty);

            $validated['branch_id'] = $branchId;
            $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);

            $mainSale = Sale::create([
                'product'        => $validated['product_name'],
                'type'           => $validated['product_type'],
                'quantity'       => $requestedQty,
                'sale_date'      => now(),
                'payment_method' => match ($validated['payment_method']) {
                    'نقدي'  => 'cash',
                    'تطبيق' => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'cash_amount' => $validated['cash_amount'],
                'app_amount'  => $validated['bank_amount'],
                'branch_id'   => $branchId,
                'is_returned' => false,
                'notes'       => '✅ مبيعة من معرض الجوال',
                'created_at'  => $validated['created_at'] ?? now(),
            ]);

            $mobileSaleData = $validated;
            $mobileSaleData['linked_sale_id'] = $mainSale->id;
            if (!empty($validated['created_at'])) {
                $mobileSaleData['created_at'] = $validated['created_at'];
            }
            MobileSale::create($mobileSaleData);

            DB::commit();
            return redirect()->route('mobile-shop.sales.index')
                ->with('success', 'تم إضافة المبيعة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function salesEdit(MobileSale $sale)
    {
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
            'product_name'   => 'required|string|max:255',
            'product_type'   => 'required|string|max:255',
            'quantity'       => 'required|integer|min:1',
            'payment_method' => 'required|in:نقدي,تطبيق,مختلط',
            'cash_amount'    => 'required|numeric|min:0',
            'bank_amount'    => 'required|numeric|min:0',
            'created_at'     => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $oldQuantity = $sale->quantity;
            $newQuantity = $validated['quantity'];
            $branchId = auth()->user()->branch_id;

            if ($sale->product_name !== $validated['product_name'] ||
                $sale->product_type !== $validated['product_type']) {

                $oldMobileInventory = MobileInventory::where('product_name', $sale->product_name)
                    ->where('model_type', $sale->product_type)
                    ->where('branch_id', $branchId)->lockForUpdate()->first();
                if ($oldMobileInventory) $oldMobileInventory->increment('quantity', $oldQuantity);

                $oldCatalog = CatalogItem::where('product', $sale->product_name)
                    ->where('type', $sale->product_type)
                    ->where('branch_id', $branchId)->lockForUpdate()->first();
                if ($oldCatalog) $oldCatalog->increment('quantity', $oldQuantity);

                $newMobileInventory = MobileInventory::where('product_name', $validated['product_name'])
                    ->where('model_type', $validated['product_type'])
                    ->where('branch_id', $branchId)->lockForUpdate()->first();
                if (!$newMobileInventory || $newMobileInventory->quantity < $newQuantity) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في مخزون معرض الجوال'])
                        ->withInput();
                }
                $newMobileInventory->decrement('quantity', $newQuantity);

                $newCatalog = CatalogItem::where('product', $validated['product_name'])
                    ->where('type', $validated['product_type'])
                    ->where('branch_id', $branchId)->lockForUpdate()->first();
                if (!$newCatalog || $newCatalog->quantity < $newQuantity) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في الكتالوج الرئيسي'])
                        ->withInput();
                }
                $newCatalog->decrement('quantity', $newQuantity);

            } elseif ($oldQuantity !== $newQuantity) {
                $delta = $newQuantity - $oldQuantity;

                $mobileInventoryItem = MobileInventory::where('product_name', $validated['product_name'])
                    ->where('model_type', $validated['product_type'])
                    ->where('branch_id', $branchId)->lockForUpdate()->first();

                $catalogItem = CatalogItem::where('product', $validated['product_name'])
                    ->where('type', $validated['product_type'])
                    ->where('branch_id', $branchId)->lockForUpdate()->first();

                if (!$mobileInventoryItem) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['quantity' => 'المنتج غير موجود في مخزون معرض الجوال'])->withInput();
                }
                if (!$catalogItem) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['quantity' => 'المنتج غير موجود في الكتالوج الرئيسي'])->withInput();
                }

                if ($delta > 0) {
                    if ($mobileInventoryItem->quantity < $delta) {
                        DB::rollBack();
                        return redirect()->back()->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في مخزون معرض الجوال'])->withInput();
                    }
                    if ($catalogItem->quantity < $delta) {
                        DB::rollBack();
                        return redirect()->back()->withErrors(['quantity' => 'الكمية المطلوبة غير متوفرة في الكتالوج الرئيسي'])->withInput();
                    }
                    $mobileInventoryItem->decrement('quantity', $delta);
                    $catalogItem->decrement('quantity', $delta);
                } else {
                    $mobileInventoryItem->increment('quantity', -$delta);
                    $catalogItem->increment('quantity', -$delta);
                }
            }

            $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            $updateData = $validated;
            if (!empty($validated['created_at'])) {
                $updateData['created_at'] = $validated['created_at'];
            }
            $sale->update($updateData);

            // نحدّث نفس البيانات على السجل المرتبط بجدول المبيعات الرئيسي حتى ما يضل نسخة قديمة
            if ($sale->linked_sale_id && ($linkedSale = Sale::find($sale->linked_sale_id))) {
                $linkedSale->update([
                    'product'        => $validated['product_name'],
                    'type'           => $validated['product_type'],
                    'quantity'       => $newQuantity,
                    'payment_method' => match ($validated['payment_method']) {
                        'نقدي'  => 'cash',
                        'تطبيق' => 'app',
                        'مختلط' => 'mixed',
                        default => 'cash'
                    },
                    'cash_amount' => $validated['cash_amount'],
                    'app_amount'  => $validated['bank_amount'],
                ]);
            }

            DB::commit();
            return redirect()->route('mobile-shop.sales.index')->with('success', 'تم تحديث المبيعة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function salesDestroy(MobileSale $sale)
    {
        DB::beginTransaction();
        try {
            $branchId = auth()->user()->branch_id;

            $mobileInventoryItem = MobileInventory::where('product_name', $sale->product_name)
                ->where('model_type', $sale->product_type)
                ->where('branch_id', $branchId)->first();
            if ($mobileInventoryItem) $mobileInventoryItem->increment('quantity', $sale->quantity);

            $catalogItem = CatalogItem::where('product', $sale->product_name)
                ->where('type', $sale->product_type)
                ->where('branch_id', $branchId)->first();
            if ($catalogItem) $catalogItem->increment('quantity', $sale->quantity);

            if ($sale->linked_sale_id) {
                Sale::whereKey($sale->linked_sale_id)->delete();
            }

            $sale->delete();

            DB::commit();
            return redirect()->route('mobile-shop.sales.index')->with('success', 'تم حذف المبيعة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ===== الصيانة =====
    public function maintenanceIndex()
    {
        if (!Schema::hasTable('mobile_maintenance')) {
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
            'customer_name'       => 'required|string|max:255',
            'phone_number'        => 'required|string|max:20',
            'problem_description' => 'required|string',
            'mobile_type'         => 'required|string|max:255',
            'payment_method'      => 'required|in:نقدي,تطبيق,مختلط',
            'cash_amount'         => 'required|numeric|min:0',
            'bank_amount'         => 'required|numeric|min:0',
            'delivery_date'       => 'nullable|date',
            'receipt_date'        => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['branch_id'] = auth()->user()->branch_id;
            $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);

            $mainRepair = Repair::create([
                'customer_name'  => $validated['customer_name'],
                'phone'          => $validated['phone_number'],
                'device_name'    => 'جوال - ' . $validated['mobile_type'],
                'model'          => $validated['mobile_type'],
                'issue'          => $validated['problem_description'],
                'received_date'  => now(),
                'delivery_date'  => $validated['delivery_date'] ?? null,
                'cost_cash'      => $validated['cash_amount'],
                'cost_bank'      => $validated['bank_amount'],
                'payment_method' => match ($validated['payment_method']) {
                    'نقدي'  => 'cash',
                    'تطبيق' => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'received_by' => auth()->user()->name,
                'branch_id'   => auth()->user()->branch_id,
                'is_returned' => false,
                'notes'       => 'تم إضافتها من معرض الجوال',
            ]);

            $validated['linked_repair_id'] = $mainRepair->id;
            MobileMaintenance::create($validated);

            DB::commit();
            return redirect()->route('mobile-shop.maintenance.index')->with('success', 'تم إضافة الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ===== المخزون =====
    public function inventoryIndex()
    {
        $branchId = auth()->user()->branch_id;
        $inventory = MobileInventory::where('branch_id', $branchId)
            ->orderBy('product_name')
            ->orderBy('model_type')
            ->paginate(15);
        return view('mobile-shop.inventory.index', compact('inventory'));
    }

    public function inventoryCreate()
    {
        return view('mobile-shop.inventory.create');
    }

    public function inventoryStore(Request $request)
    {
        $validated = $request->validate([
            'product_name'    => 'required|string|max:255',
            'model_type'      => 'required|string|max:255',
            'quantity'        => 'required|integer|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $validated['branch_id'] = auth()->user()->branch_id;

            MobileInventory::create($validated);

            CatalogItem::updateOrCreate(
                [
                    'product'   => $validated['product_name'],
                    'type'      => $validated['model_type'],
                    'branch_id' => auth()->user()->branch_id,
                ],
                [
                    'quantity'          => DB::raw('COALESCE(quantity, 0) + ' . (int) $validated['quantity']),
                    'wholesale_price'   => $validated['wholesale_price'],
                    'sale_price'        => $validated['selling_price'],
                    'is_mobile_product' => true,
                ]
            );

            DB::commit();
            return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم إضافة المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function inventoryEdit(MobileInventory $inventory)
    {
        return view('mobile-shop.inventory.edit', compact('inventory'));
    }

    public function inventoryUpdate(Request $request, MobileInventory $inventory)
    {
        $validated = $request->validate([
            'product_name'    => 'required|string|max:255',
            'model_type'      => 'required|string|max:255',
            'quantity'        => 'required|integer|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $branchId = auth()->user()->branch_id;
            $oldQuantity = (int) $inventory->quantity;
            $newQuantity = (int) $validated['quantity'];
            $delta = $newQuantity - $oldQuantity;

            $catalogItem = CatalogItem::firstOrCreate(
                ['product' => $validated['product_name'], 'type' => $validated['model_type'], 'branch_id' => $branchId],
                ['quantity' => 0, 'wholesale_price' => $validated['wholesale_price'], 'sale_price' => $validated['selling_price'], 'is_mobile_product' => true]
            );

            if ($delta !== 0) {
                if ($delta > 0) {
                    $catalogItem->increment('quantity', $delta);
                } else {
                    $catalogItem->update(['quantity' => max(0, $catalogItem->quantity - (-$delta))]);
                }
            }

            $catalogItem->update([
                'wholesale_price' => $validated['wholesale_price'],
                'sale_price'      => $validated['selling_price'],
            ]);

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

            $catalogItem = CatalogItem::where('product', $inventory->product_name)
                ->where('type', $inventory->model_type)
                ->where('branch_id', $branchId)->first();

            if ($catalogItem) {
                $catalogItem->update(['quantity' => max(0, (int) $catalogItem->quantity - $qty)]);
            }

            $inventory->delete();

            DB::commit();
            return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم حذف المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ===== الديون =====
    public function debtsIndex()
    {
        if (!Schema::hasTable('mobile_debts')) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $debts = new LengthAwarePaginator([], 0, 15, $currentPage);
            return view('mobile-shop.debts.index', compact('debts'));
        }

        $debts = MobileDebt::where('branch_id', auth()->user()->branch_id ?? null)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('mobile-shop.debts.index', compact('debts'));
    }

    public function debtsCreate()
    {
        return view('mobile-shop.debts.create');
    }

    public function debtsStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20',
            'type'          => 'required|string|max:255',
            'cash_amount'   => 'required|numeric|min:0',
            'bank_amount'   => 'required|numeric|min:0',
            'debt_date'     => 'required|date',
            'payment_date'  => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['total'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            $validated['branch_id'] = auth()->user()->branch_id;

            $mainDebt = Debt::create([
                'customer_name' => $validated['customer_name'],
                'phone'         => $validated['phone_number'],
                // العميل مدين لمعرض الجوال (اشترى بالدين) = دين لنا = "دائن" بمقياس جدول الديون الرئيسي
                'type'          => 'دائن',
                'cash_amount'   => $validated['cash_amount'],
                'bank_amount'   => $validated['bank_amount'],
                'reason'        => 'دين من معرض الجوال - ' . $validated['type'],
                'debt_date'     => $validated['debt_date'],
                'payment_date'  => $validated['payment_date'] ?? null,
                'branch_id'     => auth()->user()->branch_id,
            ]);

            $validated['linked_debt_id'] = $mainDebt->id;
            MobileDebt::create($validated);

            DB::commit();
            return redirect()->route('mobile-shop.debts.index')->with('success', 'تم إضافة الدين بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function debtsEdit(MobileDebt $debt)
    {
        return view('mobile-shop.debts.edit', compact('debt'));
    }

    /**
     * تحديث الدين في معرض الجوال + مزامنة مع جدول الديون الرئيسي
     */
    public function debtsUpdate(Request $request, MobileDebt $debt)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20',
            'type'          => 'required|string|max:255',
            'cash_amount'   => 'required|numeric|min:0',
            'bank_amount'   => 'required|numeric|min:0',
            'debt_date'     => 'required|date',
            'payment_date'  => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['total'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);

            // الدين المرتبط بجدول الديون الرئيسي — عن طريق linked_debt_id مباشرة، مش تخمين بالاسم/التاريخ
            $linkedDebt = $debt->linked_debt_id ? Debt::find($debt->linked_debt_id) : null;

            // تحديث الدين في معرض الجوال
            $debt->update($validated);

            // تحديث الدين المرتبط في الجدول الرئيسي إذا وُجد
            if ($linkedDebt) {
                $linkedDebt->update([
                    'customer_name' => $validated['customer_name'],
                    'phone'         => $validated['phone_number'],
                    'cash_amount'   => $validated['cash_amount'],
                    'bank_amount'   => $validated['bank_amount'],
                    'reason'        => 'دين من معرض الجوال - ' . $validated['type'],
                    'debt_date'     => $validated['debt_date'],
                    'payment_date'  => $validated['payment_date'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('mobile-shop.debts.index')->with('success', 'تم تحديث الدين بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function debtsDestroy(MobileDebt $debt)
    {
        DB::beginTransaction();
        try {
            // حذف الدين المرتبط من الجدول الرئيسي — عن طريق linked_debt_id، حتى ما نحذف ديون تانية بالغلط
            if ($debt->linked_debt_id) {
                Debt::whereKey($debt->linked_debt_id)->delete();
            }

            $debt->delete();

            DB::commit();
            return redirect()->route('mobile-shop.debts.index')->with('success', 'تم حذف الدين بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ===== المصروفات =====
    public function expensesIndex()
    {
        if (!Schema::hasTable('mobile_expenses')) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $expenses = new LengthAwarePaginator([], 0, 15, $currentPage);
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
            'category'       => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'quantity'       => 'required|integer|min:1',
            'payment_method' => 'required|in:نقدي,بنكي,مختلط',
            'cash_amount'    => 'required|numeric|min:0',
            'bank_amount'    => 'required|numeric|min:0',
            'expense_date'   => 'required|date',
            'supplier_name'  => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'id_photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'reference'      => 'nullable|string|max:255',
            'defect'         => 'nullable|string|max:255',
            'return_date'    => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['total'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            $validated['branch_id'] = auth()->user()->branch_id;

            if ($request->hasFile('id_photo')) {
                $file = $request->file('id_photo');
                $destinationPath = public_path('uploads/mobile-expenses');
                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                $validated['id_photo'] = 'uploads/mobile-expenses/' . $filename;
            }

            $mainPurchase = Purchase::create([
                'item'           => $validated['category'],
                'type'           => $validated['type'],
                'quantity'       => $validated['quantity'],
                'payment_method' => match ($validated['payment_method']) {
                    'نقدي'  => 'cash',
                    'بنكي'  => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'amount_cash'   => $validated['cash_amount'],
                'amount_bank'   => $validated['bank_amount'],
                'purchase_date' => $validated['expense_date'],
                'supplier_name' => $validated['supplier_name'] ?? null,
                'phone'         => $validated['supplier_phone'] ?? null,
                'id_image'      => $validated['id_photo'] ?? null,
                'notes'         => 'تم إضافتها من معرض الجوال - ' . ($validated['notes'] ?? ''),
                'branch_id'     => auth()->user()->branch_id,
                'is_returned'   => false,
            ]);

            $validated['linked_purchase_id'] = $mainPurchase->id;
            MobileExpense::create($validated);

            DB::commit();
            return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم إضافة المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function expensesEdit(MobileExpense $expense)
    {
        return view('mobile-shop.expenses.edit', compact('expense'));
    }

    public function expensesUpdate(Request $request, MobileExpense $expense)
    {
        $validated = $request->validate([
            'category'       => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'quantity'       => 'required|integer|min:1',
            'payment_method' => 'required|in:نقدي,بنكي,مختلط',
            'cash_amount'    => 'required|numeric|min:0',
            'bank_amount'    => 'required|numeric|min:0',
            'expense_date'   => 'required|date',
            'supplier_name'  => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'reference'      => 'nullable|string|max:255',
            'defect'         => 'nullable|string|max:255',
            'return_date'    => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        $validated['total'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
        $expense->update($validated);

        // نحدّث نفس البيانات على السجل المرتبط بجدول المشتريات الرئيسي حتى ما يضل نسخة قديمة
        if ($expense->linked_purchase_id && ($linkedPurchase = Purchase::find($expense->linked_purchase_id))) {
            $linkedPurchase->update([
                'item'           => $validated['category'],
                'type'           => $validated['type'],
                'quantity'       => $validated['quantity'],
                'payment_method' => match ($validated['payment_method']) {
                    'نقدي'  => 'cash',
                    'بنكي'  => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'amount_cash'   => $validated['cash_amount'],
                'amount_bank'   => $validated['bank_amount'],
                'purchase_date' => $validated['expense_date'],
                'supplier_name' => $validated['supplier_name'] ?? null,
                'phone'         => $validated['supplier_phone'] ?? null,
            ]);
        }

        return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم تحديث المصروف بنجاح');
    }

    public function expensesDestroy(MobileExpense $expense)
    {
        if ($expense->linked_purchase_id) {
            Purchase::whereKey($expense->linked_purchase_id)->delete();
        }

        $expense->delete();
        return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم حذف المصروف بنجاح');
    }

    // ===== الصفحة الرئيسية =====
    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $maintenanceCount = Schema::hasTable('mobile_maintenance')
            ? MobileMaintenance::where('branch_id', $branchId)->count() : 0;
        $salesCount = Schema::hasTable('mobile_sales')
            ? MobileSale::where('branch_id', $branchId)->count() : 0;
        $inventoryCount = Schema::hasTable('mobile_inventory')
            ? MobileInventory::where('branch_id', $branchId)->sum('quantity') : 0;
        $debtsCount = Schema::hasTable('mobile_debts')
            ? MobileDebt::where('branch_id', $branchId)->whereNull('payment_date')->count() : 0;
        $expensesCount = Schema::hasTable('mobile_expenses')
            ? MobileExpense::where('branch_id', $branchId)->count() : 0;
        $totalMaintenance = Schema::hasTable('mobile_maintenance')
            ? MobileMaintenance::where('branch_id', $branchId)->sum('cost') : 0;
        $totalSales = Schema::hasTable('mobile_sales')
            ? MobileSale::where('branch_id', $branchId)->sum('cost') : 0;
        $totalDebts = Schema::hasTable('mobile_debts')
            ? MobileDebt::where('branch_id', $branchId)->whereNull('payment_date')->sum('total') : 0;
        $totalExpenses = Schema::hasTable('mobile_expenses')
            ? MobileExpense::where('branch_id', $branchId)->sum('total') : 0;

        return view('mobile-shop.index', compact(
            'maintenanceCount', 'salesCount', 'inventoryCount',
            'debtsCount', 'expensesCount', 'totalMaintenance',
            'totalSales', 'totalDebts', 'totalExpenses'
        ));
    }
}