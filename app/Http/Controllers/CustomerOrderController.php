<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of customer orders
     */
    public function index()
    {
        $orders = CustomerOrder::latest()->paginate(15);

        return view('customer-orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        return view('customer-orders.create');
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'device_type' => 'required|string|max:255',
            'specifications' => 'required|string',
            'approximate_price' => 'required|string|max:100',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        CustomerOrder::create($validated);

        return redirect()->route('customer-orders.index')
            ->with('success', 'تم إضافة الطلب بنجاح');
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(CustomerOrder $customerOrder)
    {
        return view('customer-orders.edit', compact('customerOrder'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, CustomerOrder $customerOrder)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'device_type' => 'required|string|max:255',
            'specifications' => 'required|string',
            'approximate_price' => 'required|string|max:100',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $customerOrder->update($validated);

        return redirect()->route('customer-orders.index')
            ->with('success', 'تم تحديث الطلب بنجاح');
    }

    /**
     * Remove the specified order
     */
    public function destroy(CustomerOrder $customerOrder)
    {
        $customerOrder->delete();

        return redirect()->route('customer-orders.index')
            ->with('success', 'تم حذف الطلب بنجاح');
    }

    /**
     * Show the specified order details
     */
    public function show(CustomerOrder $customerOrder)
    {
        return view('customer-orders.show', compact('customerOrder'));
    }
}
