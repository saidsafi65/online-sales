<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAccountController extends Controller
{
    protected function customer()
    {
        return Auth::guard('customer')->user();
    }

    /**
     * الصفحة الرئيسية لحساب العميل (نظرة عامة)
     */
    public function show()
    {
        $customer = $this->customer();
        $recentOrders = $customer->orders()->latest()->take(5)->get();

        return view('customer.account', compact('customer', 'recentOrders'));
    }

    /**
     * تعديل البيانات الشخصية
     */
    public function editProfile()
    {
        return view('customer.profile', ['customer' => $this->customer()]);
    }

    public function updateProfile(Request $request)
    {
        $customer = $this->customer();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:50|unique:customers,phone,' . $customer->id,
            'email'   => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        return back()->with('success', 'تم تحديث بياناتك بنجاح');
    }

    /**
     * تغيير كلمة المرور
     */
    public function editPassword()
    {
        return view('customer.password');
    }

    public function updatePassword(Request $request)
    {
        $customer = $this->customer();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password'          => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $customer->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $customer->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * سجل الطلبات
     */
    public function orders()
    {
        $orders = $this->customer()->orders()->latest()->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    /**
     * تفاصيل طلب واحد
     */
    public function showOrder(Order $order)
    {
        abort_unless($order->customer_id === $this->customer()->id, 403);

        $order->load('items', 'payment');

        return view('customer.order-details', compact('order'));
    }
}