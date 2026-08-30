<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isCompanyUser() || ! $user->company_id) {
            abort(403, "Accès réservé aux comptes entreprise.");
        }

        if (! $user->company || ! $user->company->isActive()) {
            abort(403, "Ce compte entreprise est suspendu.");
        }

        return $next($request);
    }
}
