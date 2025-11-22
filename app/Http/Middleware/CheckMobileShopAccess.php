<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMobileShopAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // إذا كان الموظف مخصص لمعرض الجوال فقط
        if ($user->is_mobile_shop_only) {
            // السماح فقط بالوصول إلى صفحات معرض الجوال
            $allowedRoutes = [
                'mobile-shop.index',
                'mobile-shop.maintenance.*',
                'mobile-shop.sales.*',
                'mobile-shop.inventory.*',
                'mobile-shop.debts.*',
                'mobile-shop.expenses.*',
                'dashboard',
                'logout',
                'profile.*'
            ];
            
            $currentRoute = $request->route()->getName();
            
            foreach ($allowedRoutes as $pattern) {
                if (fnmatch($pattern, $currentRoute)) {
                    return $next($request);
                }
            }
            
            // إعادة توجيه إلى معرض الجوال
            return redirect()->route('mobile-shop.index')->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }
        
        return $next($request);
    }
}