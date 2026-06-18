<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * @param  list<string>  $permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasAnyPermission($permissions)) {
            if ($request->expectsJson()) {
                abort(403, __('messages.errors.access_denied'));
            }

            return redirect($user->homeRoute())
                ->with('error', __('messages.errors.access_denied'));
        }

        return $next($request);
    }
}
