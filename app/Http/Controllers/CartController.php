<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected function getOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(['customer_id' => Auth::guard('customer')->id()]);
    }

    public function index()
    {
        $cart = $this->getOrCreateCart()->load('items.product');
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
    
        if ($product->is_out_of_stock) {
            return $request->wantsJson()
                ? response()->json(['error' => 'هذا المنتج غير متوفر حالياً'], 422)
                : back()->with('error', 'هذا المنتج غير متوفر حالياً');
        }
    
        $cart = $this->getOrCreateCart();
    
        $item = CartItem::firstOrNew([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
        ]);
    
        $item->price    = $product->final_price;
        $item->quantity = $item->exists ? $item->quantity + $quantity : $quantity;
        $item->save();
    
        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => 'تمت الإضافة للسلة'])
            : back()->with('success', 'تمت الإضافة للسلة');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeItem($cartItem);

        $quantity = max(1, (int) $request->input('quantity', 1));
        $cartItem->update(['quantity' => $quantity]);

        return back()->with('success', 'تم تحديث الكمية');
    }

    public function remove(CartItem $cartItem)
    {
        $this->authorizeItem($cartItem);
        $cartItem->delete();

        return back()->with('success', 'تم الحذف من السلة');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:40']);

        $cart = $this->getOrCreateCart()->load('items');
        $code = trim($request->input('code'));
        $coupon = Coupon::whereRaw('LOWER(code) = ?', [mb_strtolower($code)])->first();

        if (! $coupon) {
            return back()->with('error', 'كود الخصم غير موجود');
        }
        if (! $coupon->isValidFor($cart->total)) {
            $reason = ! $coupon->is_active ? 'كود الخصم غير مفعّل'
                : ($coupon->expires_at && $coupon->expires_at->isPast() ? 'كود الخصم منتهي الصلاحية'
                : ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses ? 'كود الخصم وصل الحد الأقصى للاستخدام'
                : 'الحد الأدنى للطلب لاستخدام هذا الكود ' . number_format((float) $coupon->min_order_amount, 2) . ' شيكل'));

            return back()->with('error', $reason);
        }

        $cart->update(['coupon_code' => $coupon->code]);

        return back()->with('success', 'تم تطبيق كود الخصم بنجاح');
    }

    public function removeCoupon()
    {
        $this->getOrCreateCart()->update(['coupon_code' => null]);

        return back()->with('success', 'تم إلغاء كود الخصم');
    }

    protected function authorizeItem(CartItem $cartItem): void
    {
        abort_unless(
            $cartItem->cart->customer_id === Auth::guard('customer')->id(),
            403
        );
    }
}