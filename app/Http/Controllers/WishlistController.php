<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        $products = Product::whereIn('id', Wishlist::where('customer_id', $customerId)->pluck('product_id'))
            ->latest()
            ->paginate(12);

        return view('customer.wishlist', compact('products'));
    }

    public function toggle(Product $product)
    {
        $customerId = Auth::guard('customer')->id();

        $existing = Wishlist::where('customer_id', $customerId)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'تم إزالة المنتج من المفضلة';
        } else {
            Wishlist::create(['customer_id' => $customerId, 'product_id' => $product->id]);
            $message = 'تمت إضافة المنتج للمفضلة';
        }

        return back()->with('success', $message);
    }
}
