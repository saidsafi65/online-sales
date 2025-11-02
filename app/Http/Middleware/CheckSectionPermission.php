<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $section): Response
    {
        $user = $request->user();
        if (!$user || !$user->canViewSection($section)) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الصفحة');
        }
        return $next($request);
    }
}
