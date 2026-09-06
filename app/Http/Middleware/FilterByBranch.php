<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilterByBranch {
    public function handle(Request $request, Closure $next) {
        $user = auth()->user();

        // المدير يمكنه رؤية جميع البيانات
        if ($user->isAdmin()) {
            return $next($request);
        }

        // الموظفون يرون فقط بيانات فرعهم
        // سنضيف هذا الفيلتر تلقائياً في الـ Queries
        $request->attributes->set('user_branch_id', $user->branch_id);

        return $next($request);
    }
}