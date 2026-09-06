<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBranchAccess {
    public function handle(Request $request, Closure $next) {
        $user = auth()->user();

        // المدير له صلاحية الوصول لكل شيء
        if ($user->isAdmin()) {
            return $next($request);
        }

        // الموظف يمكنه الوصول فقط لبيانات فرعه
        $branchId = $request->route('branch_id') ?? $request->input('branch_id');
        
        if ($branchId && $branchId != $user->branch_id) {
            abort(403, 'ليس لديك صلاحية للوصول لهذا الفرع');
        }

        return $next($request);
    }
}