<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePasswordIsSet
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->needs_password && ! $request->routeIs('account.password.set')) {
            return redirect()->route('account.password.set');
        }

        return $next($request);
    }
}