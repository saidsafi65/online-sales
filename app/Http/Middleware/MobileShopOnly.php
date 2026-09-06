<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class MobileShopOnly
{
    public function handle(Request $request, Closure $next)
    {
        // تحقق من أن المستخدم مسموح له بالوصول لمعرض الجوال
        // يمكنك تعديل الشرط حسب منطق التطبيق
        
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // إذا كان هناك شرط معين للسماح بالوصول (مثلاً دور معين)
        if (!auth()->user()->can_view_mobile_shop) {
            abort(403, 'غير مصرح لك بالوصول لمعرض الجوال');
        }

        return $next($request);
    }
}