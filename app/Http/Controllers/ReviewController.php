<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $customerId = Auth::guard('customer')->id();

        // نتأكد من الأهلية بالسيرفر دايمًا (مش بس نخفي الفورم بالواجهة) — لازم يكون
        // عنده طلب فعلي لهالمنتج وصل مرحلة دفع ناجحة على الأقل.
        $orderItem = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId)
                    ->whereIn('status', Order::STATUS_COUNTS_AS_PURCHASED);
            })
            ->first();

        if (! $orderItem) {
            return back()->with('error', 'لازم تشتري المنتج قبل ما تقدر تقيّمه');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'التقييم مطلوب',
        ]);

        Review::updateOrCreate(
            ['customer_id' => $customerId, 'product_id' => $product->id],
            [
                'order_item_id' => $orderItem->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with('success', 'شكراً لتقييمك!');
    }
}
