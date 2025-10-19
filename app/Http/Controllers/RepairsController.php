<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RepairsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Repair::query();

        // إذا كان المستخدم ليس مدير نظام، اعرض فقط صيانة فرعه
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
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('issue', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $repairs = $query->orderByDesc('received_date')
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('repairs.index', compact('repairs'));
    }

    public function create(): View
    {
        return view('repairs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'customer_name' => 'required|string|max:255',
            'device_name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'issue' => 'required|string',
            'phone' => 'required|string|max:20',
            'received_date' => 'required|date',
            'cost_cash' => 'required|numeric|min:0',
            'cost_bank' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,app,mixed',
            'delivery_date' => 'nullable|date',
            'received_by' => 'required|string|max:255',
            'is_returned' => 'nullable|boolean',
            'return_reason' => 'nullable|string',
            'return_date' => 'nullable|date',
            'return_cost' => 'nullable|numeric|min:0',
            'return_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            Repair::create([
                'customer_name' => $request->customer_name,
                'device_name' => $request->device_name,
                'model' => $request->model,
                'issue' => $request->issue,
                'phone' => $request->phone,
                'received_date' => $request->received_date,
                'cost_cash' => $request->cost_cash,
                'cost_bank' => $request->cost_bank,
                'payment_method' => $request->payment_method,
                'delivery_date' => $request->delivery_date,
                'received_by' => $request->received_by,
                'is_returned' => (bool) $request->is_returned,
                'return_reason' => $request->return_reason,
                'return_date' => $request->return_date,
                'return_cost' => $request->return_cost,
                'return_delivery_date' => $request->return_delivery_date,
                'branch_id' => auth()->user()->branch_id, // ✅ أضف الفرع
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('repairs.index')->with('success', 'تم إضافة الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الصيانة: '.$e->getMessage())->withInput();
        }
    }

    public function edit(Repair $repair): View
    {
        return view('repairs.edit', compact('repair'));
    }

    public function update(Request $request, Repair $repair): RedirectResponse
    {
        $rules = [
            'customer_name' => 'required|string|max:255',
            'device_name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'issue' => 'required|string',
            'phone' => 'required|string|max:20',
            'received_date' => 'required|date',
            'cost_cash' => 'required|numeric|min:0',
            'cost_bank' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,app,mixed',
            'delivery_date' => 'nullable|date',
            'received_by' => 'required|string|max:255',
            'is_returned' => 'nullable|boolean',
            'return_reason' => 'nullable|string',
            'return_date' => 'nullable|date',
            'return_cost' => 'nullable|numeric|min:0',
            'return_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $repair->update([
                'customer_name' => $request->customer_name,
                'device_name' => $request->device_name,
                'model' => $request->model,
                'issue' => $request->issue,
                'phone' => $request->phone,
                'received_date' => $request->received_date,
                'cost_cash' => $request->cost_cash,
                'cost_bank' => $request->cost_bank,
                'payment_method' => $request->payment_method,
                'delivery_date' => $request->delivery_date,
                'received_by' => $request->received_by,
                'is_returned' => (bool) $request->is_returned,
                'return_reason' => $request->return_reason,
                'return_date' => $request->return_date,
                'return_cost' => $request->return_cost,
                'return_delivery_date' => $request->return_delivery_date,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('repairs.index')->with('success', 'تم تحديث بيانات الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ ��ثناء تحديث بيانات الصيانة: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(Repair $repair): RedirectResponse
    {
        $repair->delete();

        return redirect()->route('repairs.index')->with('success', 'تم حذف عملية الصيانة');
    }
}
