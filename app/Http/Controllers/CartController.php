<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
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

    protected function authorizeItem(CartItem $cartItem): void
    {
        abort_unless(
            $cartItem->cart->customer_id === Auth::guard('customer')->id(),
            403
        );
    }
}