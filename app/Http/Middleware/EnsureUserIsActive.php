<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Logs out and blocks any request from a staff account that's been disabled
     * since login — otherwise a disabled account keeps full access until its
     * session cookie naturally expires (login-time is the only place isActive()
     * was previously checked).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'isActive') && ! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'هذا الحساب معطّل حالياً']);
        }

        return $next($request);
    }
}
