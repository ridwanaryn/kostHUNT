<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $allowed = array_map(
            static fn (string $role): ?UserRole => UserRole::tryFrom($role),
            $roles,
        );

        if (! in_array($user->role, $allowed, true)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
