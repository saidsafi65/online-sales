<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictMobileShopUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user && $user->is_mobile_shop_only && $user->canViewSection('mobile_shop')) {
            return redirect()->route('mobile-shop.index')
                ->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }
        
        return $next($request);
    }
}