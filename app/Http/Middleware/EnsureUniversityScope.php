<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the entire "university.*" route group. The Eloquent global scope
 * (UniversityScope) keeps queries tenant-safe, but this middleware is the
 * first line of defense: it refuses entry outright to any account that has
 * no university attached, or whose university has been suspended by the
 * Super Admin — before a single query runs.
 */
class EnsureUniversityScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isUniversityAdmin() && ! $user->isSuperAdmin())) {
            abort(403, "Accès réservé au personnel universitaire.");
        }

        if ($user->isUniversityAdmin()) {
            if (! $user->university_id) {
                abort(403, "Aucune université n'est associée à ce compte.");
            }

            if (! $user->university || ! $user->university->isActive()) {
                abort(403, "L'université associée à ce compte est suspendue.");
            }
        }

        return $next($request);
    }
}
