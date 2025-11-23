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
            
            // إنشاء في جدول معرض الجوال
            $mobileMaintenance = MobileMaintenance::create($validated);

            // إضافة تلقائياً في جدول الصيانة الرئيسي
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
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function maintenanceEdit(MobileMaintenance $maintenance)
    {
        return view('mobile-shop.maintenance.edit', compact('maintenance'));
    }

    public function maintenanceUpdate(Request $request, MobileMaintenance $maintenance)
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

        $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
        $maintenance->update($validated);
        return redirect()->route('mobile-shop.maintenance.index')->with('success', 'تم تحديث الصيانة بنجاح');
    }

    public function maintenanceDestroy(MobileMaintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('mobile-shop.maintenance.index')->with('success', 'تم حذف الصيانة بنجاح');
    }

    // ===== المبيعات =====
    public function salesIndex()
    {
        if (! Schema::hasTable('mobile_sales')) {
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
        return view('mobile-shop.sales.create');
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
        ]);

        DB::beginTransaction();
        try {
            $validated['branch_id'] = auth()->user()->branch_id;
            $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
            
            // إنشاء في جدول معرض الجوال
            MobileSale::create($validated);

            // إضافة تلقائياً في جدول المبيعات الرئيسي
            Sale::create([
                'product' => $validated['product_name'],
                'type' => $validated['product_type'],
                'quantity' => $validated['quantity'],
                'sale_date' => now(),
                'payment_method' => match($validated['payment_method']) {
                    'نقدي' => 'cash',
                    'تطبيق' => 'app',
                    'مختلط' => 'mixed',
                    default => 'cash'
                },
                'cash_amount' => $validated['cash_amount'],
                'app_amount' => $validated['bank_amount'],
                'branch_id' => auth()->user()->branch_id,
                'is_returned' => false,
                'notes' => 'تم إضافتها من معرض الجوال'
            ]);

            DB::commit();
            return redirect()->route('mobile-shop.sales.index')->with('success', 'تم إضافة المبيعة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function salesEdit(MobileSale $sale)
    {
        return view('mobile-shop.sales.edit', compact('sale'));
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
        ]);

        $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
        $sale->update($validated);
        return redirect()->route('mobile-shop.sales.index')->with('success', 'تم تحديث المبيعة بنجاح');
    }

    public function salesDestroy(MobileSale $sale)
    {
        $sale->delete();
        return redirect()->route('mobile-shop.sales.index')->with('success', 'تم حذف المبيعة بنجاح');
    }

    // ===== المخزون =====
    public function inventoryIndex()
    {
        if (! Schema::hasTable('mobile_inventory')) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $inventory = new LengthAwarePaginator([], 0, 15, $currentPage);
            return view('mobile-shop.inventory.index', compact('inventory'));
        }

        $inventory = MobileInventory::where('branch_id', auth()->user()->branch_id ?? null)
            ->orderBy('created_at', 'desc')
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
            'product_name' => 'required|string|max:255',
            'model_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $validated['branch_id'] = auth()->user()->branch_id;
            
            // إنشاء في جدول معرض الجوال
            MobileInventory::create($validated);

            // إضافة/تحديث في الكتالوج الرئيسي
            CatalogItem::updateOrCreate(
                [
                    'product' => $validated['product_name'],
                    'type' => $validated['model_type'],
                    'branch_id' => auth()->user()->branch_id
                ],
                [
                    'quantity' => DB::raw('quantity + ' . (int)$validated['quantity']),
                    'wholesale_price' => $validated['wholesale_price'],
                    'sale_price' => $validated['selling_price'],
                    'is_mobile_product' => true
                ]
            );

            DB::commit();
            return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم إضافة المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
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

        $inventory->update($validated);
        return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function inventoryDestroy(MobileInventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم حذف المنتج بنجاح');
    }

    // ===== الديون =====
    public function debtsIndex()
    {
        if (! Schema::hasTable('mobile_debts')) {
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
            'phone_number' => 'required|string|max:20',
            'type' => 'required|string|max:255',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'debt_date' => 'required|date',
            'payment_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['total'] = $validated['cash_amount'] + $validated['bank_amount'];
            $validated['branch_id'] = auth()->user()->branch_id;
            
            // إنشاء في جدول معرض الجوال
            MobileDebt::create($validated);

            // إضافة في جدول الديون الرئيسي
            Debt::create([
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone_number'],
                'type' => 'مدين', // افتراضياً
                'cash_amount' => $validated['cash_amount'],
                'bank_amount' => $validated['bank_amount'],
                'reason' => 'دين من معرض الجوال - ' . $validated['type'],
                'debt_date' => $validated['debt_date'],
                'payment_date' => $validated['payment_date'],
                'branch_id' => auth()->user()->branch_id
            ]);

            DB::commit();
            return redirect()->route('mobile-shop.debts.index')->with('success', 'تم إضافة الدين بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function debtsEdit(MobileDebt $debt)
    {
        return view('mobile-shop.debts.edit', compact('debt'));
    }

    public function debtsUpdate(Request $request, MobileDebt $debt)
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

        $validated['total'] = $validated['cash_amount'] + $validated['bank_amount'];
        $debt->update($validated);
        return redirect()->route('mobile-shop.debts.index')->with('success', 'تم تحديث الدين بنجاح');
    }

    public function debtsDestroy(MobileDebt $debt)
    {
        $debt->delete();
        return redirect()->route('mobile-shop.debts.index')->with('success', 'تم حذف الدين بنجاح');
    }

    // ===== المصروفات =====
    public function expensesIndex()
    {
        if (! Schema::hasTable('mobile_expenses')) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $expenses = new LengthAwarePaginator([], 0, 15, $currentPage);
            return view('mobile-shop.expenses.index', compact('expenses'));
        }

        $expenses = MobileExpense::where('branch_id', auth()->user()->branch_id ?? null)
            ->orderBy('created_at', 'desc')
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
            $validated['total'] = $validated['cash_amount'] + $validated['bank_amount'];
            $validated['branch_id'] = auth()->user()->branch_id;
            
            // معالجة رفع صورة الهوية
            if ($request->hasFile('id_photo')) {
                $file = $request->file('id_photo');
                $destinationPath = public_path('uploads/mobile-expenses');
                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $file->move($destinationPath, $filename);
                $validated['id_photo'] = 'uploads/mobile-expenses/' . $filename;
            }

            // إنشاء في جدول معرض الجوال
            MobileExpense::create($validated);

            // إضافة في جدول المشتريات الرئيسي
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
                'supplier_name' => $validated['supplier_name'],
                'phone' => $validated['supplier_phone'],
                'id_image' => $validated['id_photo'] ?? null,
                'notes' => 'تم إضافتها من معرض الجوال - ' . ($validated['notes'] ?? ''),
                'branch_id' => auth()->user()->branch_id,
                'is_returned' => false
            ]);

            DB::commit();
            return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم إضافة المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function expensesEdit(MobileExpense $expense)
    {
        return view('mobile-shop.expenses.edit', compact('expense'));
    }

    public function expensesUpdate(Request $request, MobileExpense $expense)
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

        $validated['total'] = $validated['cash_amount'] + $validated['bank_amount'];
        
        if ($request->hasFile('id_photo')) {
            $file = $request->file('id_photo');
            $destinationPath = public_path('uploads/mobile-expenses');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
            $file->move($destinationPath, $filename);
            $validated['id_photo'] = 'uploads/mobile-expenses/' . $filename;
        } else {
            unset($validated['id_photo']);
        }

        $expense->update($validated);
        return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم تحديث المصروف بنجاح');
    }

    public function expensesDestroy(MobileExpense $expense)
    {
        $expense->delete();
        return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم حذف المصروف بنجاح');
    }

    // الصفحة الرئيسية لمعرض الجوال
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