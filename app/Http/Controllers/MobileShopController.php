<?php

namespace App\Http\Controllers;

use App\Models\MobileMaintenance;
use App\Models\MobileSale;
use App\Models\MobileInventory;
use App\Models\MobileDebt;
use App\Models\MobileExpense;
use Illuminate\Http\Request;

class MobileShopController extends Controller
{
    // ===== الصيانة =====
    public function maintenanceIndex()
    {
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
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        // compute unified cost for backward compatibility
        $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
        MobileMaintenance::create($validated);

        return redirect()->route('mobile-shop.maintenance.index')->with('success', 'تم إضافة الصيانة بنجاح');
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

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['cost'] = ($validated['cash_amount'] ?? 0) + ($validated['bank_amount'] ?? 0);
        MobileSale::create($validated);

        return redirect()->route('mobile-shop.sales.index')->with('success', 'تم إضافة المبيعة بنجاح');
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

        $validated['branch_id'] = auth()->user()->branch_id;
        MobileInventory::create($validated);

        return redirect()->route('mobile-shop.inventory.index')->with('success', 'تم إضافة المنتج بنجاح');
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

        $validated['total'] = $validated['cash_amount'] + $validated['bank_amount'];
        $validated['branch_id'] = auth()->user()->branch_id;
        MobileDebt::create($validated);

        return redirect()->route('mobile-shop.debts.index')->with('success', 'تم إضافة الدين بنجاح');
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

        $validated['total'] = $validated['cash_amount'] + $validated['bank_amount'];
        $validated['branch_id'] = auth()->user()->branch_id;
        // معالجة رفع صورة الهوية إن وجدت
        if ($request->hasFile('id_photo')) {
            $file = $request->file('id_photo');
            $destinationPath = public_path('uploads/mobile-expenses');
            if (! is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
            $file->move($destinationPath, $filename);
            $validated['id_photo'] = 'uploads/mobile-expenses/'.$filename;
        }

        MobileExpense::create($validated);

        return redirect()->route('mobile-shop.expenses.index')->with('success', 'تم إضافة المصروف بنجاح');
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
        // معالجة رفع صورة الهوية إن تم تحميلها
        if ($request->hasFile('id_photo')) {
            $file = $request->file('id_photo');
            $destinationPath = public_path('uploads/mobile-expenses');
            if (! is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
            $file->move($destinationPath, $filename);
            $validated['id_photo'] = 'uploads/mobile-expenses/'.$filename;
        } else {
            // لا تغير قيمة id_photo إذا لم يتم رفع ملف جديد
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
        
        $maintenanceCount = MobileMaintenance::where('branch_id', $branchId)->count();
        $salesCount = MobileSale::where('branch_id', $branchId)->count();
        $inventoryCount = MobileInventory::where('branch_id', $branchId)->sum('quantity');
        $debtsCount = MobileDebt::where('branch_id', $branchId)->where('payment_date', null)->count();
        $expensesCount = MobileExpense::where('branch_id', $branchId)->count();

        $totalMaintenance = MobileMaintenance::where('branch_id', $branchId)->sum('cost');
        $totalSales = MobileSale::where('branch_id', $branchId)->sum('cost');
        $totalDebts = MobileDebt::where('branch_id', $branchId)->where('payment_date', null)->sum('total');
        $totalExpenses = MobileExpense::where('branch_id', $branchId)->sum('total');

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
