<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderByDesc('created_at')->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $validated['code'] = mb_strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        Coupon::create($validated);

        return redirect()->route('coupons.index')->with('success', 'تم إنشاء كود الخصم بنجاح');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validator = $this->validator($request, $coupon->id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $validated['code'] = mb_strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $coupon->update($validated);

        return redirect()->route('coupons.index')->with('success', 'تم تحديث كود الخصم بنجاح');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return back()->with('success', 'تم تحديث حالة الكود');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('coupons.index')->with('success', 'تم حذف كود الخصم');
    }

    private function validator(Request $request, ?int $ignoreId = null): \Illuminate\Validation\Validator
    {
        $uniqueRule = 'unique:coupons,code' . ($ignoreId ? ',' . $ignoreId : '');

        return Validator::make($request->all(), [
            'code' => 'required|string|max:40|' . $uniqueRule,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ], [
            'code.required' => 'اكتب كود الخصم',
            'code.unique' => 'هذا الكود مستخدم أصلاً',
            'value.required' => 'اكتب قيمة الخصم',
            'value.min' => 'قيمة الخصم يجب أن تكون أكبر من صفر',
        ]);
    }
}
