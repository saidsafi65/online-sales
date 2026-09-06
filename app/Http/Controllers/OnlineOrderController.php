<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OnlineOrderController extends Controller
{
    protected array $statusFlow = Order::STATUS_LABELS;

    public function index(Request $request)
    {
        $query = Order::with('customer')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $statusCounts = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.online-orders.index', [
            'orders'        => $orders,
            'statusFlow'    => $this->statusFlow,
            'statusCounts'  => $statusCounts,
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items', 'payment', 'customer');

        return view('admin.online-orders.show', [
            'order'      => $order,
            'statusFlow' => $this->statusFlow,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys($this->statusFlow)),
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
}