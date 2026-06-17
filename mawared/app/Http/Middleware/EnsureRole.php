<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  UserRole|list<UserRole>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = collect($roles)
            ->map(fn (string $role) => UserRole::from($role))
            ->contains($user->role);

        if (! $allowed) {
            if ($request->expectsJson()) {
                abort(403, __('messages.errors.access_denied'));
            }

            return redirect($user->homeRoute())
                ->with('error', __('messages.errors.access_denied'));
        }

        return $next($request);
    }
}
