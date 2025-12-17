<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagerIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user &&
            $user->role_id === 3 &&
            $user->is_active === false
        ) {
            abort(403, 'Konto managera jest nieaktywne.');
        }

        return $next($request);
    }
}
