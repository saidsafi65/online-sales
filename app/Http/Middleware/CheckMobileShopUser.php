<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMobileShopUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user && $user->is_mobile_shop_only && $user->canViewSection('mobile_shop')) {
            $routeName = $request->route()->getName();
            
            if (!str_starts_with($routeName, 'mobile-shop.') && $routeName !== 'logout') {
                return redirect()->route('mobile-shop.index')
                    ->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
            }
        }
        
        return $next($request);
    }
}