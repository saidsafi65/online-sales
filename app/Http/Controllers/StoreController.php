<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * عرض قائمة المخزن الخارجي
     */
    public function index(Request $request)
    {
        $query = Store::query();

    if (!auth()->user()->isAdmin()) {
        $query->where('branch_id', auth()->user()->branch_id);
    }
        // البحث
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('supplier_name', 'LIKE', "%{$search}%");
            });
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        $items = $query->orderBy('date_added', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // حساب الإجماليات
        $totalCash = Store::sum('cash_amount');
        $totalBank = Store::sum('bank_amount');

        return view('store.index', compact('items', 'totalCash', 'totalBank'));
    }

    /**
     * عرض صفحة إضافة منتج جديد
     */
    public function create()
    {
        return view('store.create');
    }

    /**
     * حفظ المنتج الجديد
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_name' => 'required|string|max:255',
            'wholesale_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:نقدي,بنكي,مختلط',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'date_added' => 'required|date',
            'branch_id' => auth()->user()->branch_id, // ✅ أضف هذا
        ], [
            'product_name.required' => 'اسم المنتج مطلوب',
            'product_type.required' => 'نوع المنتج مطلوب',
            'quantity.required' => 'الكمية مطلوبة',
            'quantity.min' => 'الكمية يجب أن تكون صفر أو أكثر',
            'supplier_name.required' => 'اسم المورد مطلوب',
            'wholesale_price.required' => 'سعر الجملة مطلوب',
            'wholesale_price.min' => 'سعر الجملة يجب أن يكون صفر أو أكثر',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
            'cash_amount.required' => 'المبلغ النقدي مطلوب',
            'cash_amount.min' => 'المبلغ النقدي يجب أن يكون صفر أو أكثر',
            'bank_amount.required' => 'المبلغ البنكي مطلوب',
            'bank_amount.min' => 'المبلغ البنكي يجب أن يكون صفر أو أكثر',
            'date_added.required' => 'تاريخ الإضافة مطلوب',
            'date_added.date' => 'تاريخ الإضافة غير صحيح',
        ]);

        // التحقق من منطق طريقة الدفع
        if ($validated['payment_method'] == 'نقدي' && $validated['bank_amount'] != 0) {
            return back()->withErrors(['bank_amount' => 'عند اختيار نقدي، المبلغ البنكي يجب أن يكون صفر'])
                ->withInput();
        }

        if ($validated['payment_method'] == 'بنكي' && $validated['cash_amount'] != 0) {
            return back()->withErrors(['cash_amount' => 'عند اختيار بنكي، المبلغ النقدي يجب أن يكون صفر'])
                ->withInput();
        }

        if ($validated['payment_method'] == 'مختلط' &&
            ($validated['cash_amount'] == 0 && $validated['bank_amount'] == 0)) {
            return back()->withErrors(['payment_method' => 'عند اختيار مختلط، يجب إدخال قيمة لكل من النقدي والبنكي'])
                ->withInput();
        }

        // إنشاء المنتج
        Store::create($validated);

        return redirect()->route('store.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    /**
     * عرض صفحة تعديل المنتج
     */
    public function edit($id)
    {
        $item = Store::findOrFail($id);

        return view('store.edit', compact('item'));
    }

    /**
     * تحديث المنتج
     */
    public function update(Request $request, $id)
    {
        $item = Store::findOrFail($id);

        // التحقق من البيانات
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_name' => 'required|string|max:255',
            'wholesale_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:نقدي,بنكي,مختلط',
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'date_added' => 'required|date',
        ], [
            'product_name.required' => 'اسم المنتج مطلوب',
            'product_type.required' => 'نوع المنتج مطلوب',
            'quantity.required' => 'الكمية مطلوبة',
            'quantity.min' => 'الكمية يجب أن تكون صفر أو أكثر',
            'supplier_name.required' => 'اسم المورد مطلوب',
            'wholesale_price.required' => 'سعر الجملة مطلوب',
            'wholesale_price.min' => 'سعر الجملة يجب أن يكون صفر أو أكثر',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
            'cash_amount.required' => 'المبلغ النقدي مطلوب',
            'cash_amount.min' => 'المبلغ النقدي يجب أن يكون صفر أو أكثر',
            'bank_amount.required' => 'المبلغ البنكي مطلوب',
            'bank_amount.min' => 'المبلغ البنكي يجب أن يكون صفر أو أكثر',
            'date_added.required' => 'تاريخ الإضافة مطلوب',
            'date_added.date' => 'تاريخ الإضافة غير صحيح',
        ]);

        // التحقق من منطق طريقة الدفع
        if ($validated['payment_method'] == 'نقدي' && $validated['bank_amount'] != 0) {
            return back()->withErrors(['bank_amount' => 'عند اختيار نقدي، المبلغ البنكي يجب أن يكون صفر'])
                ->withInput();
        }

        if ($validated['payment_method'] == 'بنكي' && $validated['cash_amount'] != 0) {
            return back()->withErrors(['cash_amount' => 'عند اختيار بنكي، المبلغ النقدي يجب أن يكون صفر'])
                ->withInput();
        }

        if ($validated['payment_method'] == 'مختلط' &&
            ($validated['cash_amount'] == 0 && $validated['bank_amount'] == 0)) {
            return back()->withErrors(['payment_method' => 'عند اختيار مختلط، يجب إدخال قيمة لكل من النقدي والبنكي'])
                ->withInput();
        }

        // تحديث المنتج
        $item->update($validated);

        return redirect()->route('store.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * حذف المنتج
     */
    public function destroy($id)
    {
        $item = Store::findOrFail($id);
        $item->delete();

        return redirect()->route('store.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }
}
