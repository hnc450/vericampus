<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Tenant isolation, enforced at query level rather than trusted to controllers.
 *
 * - Super admin: unrestricted (needs cross-university visibility).
 * - University admin / staff: forced to their own university_id, even if a
 *   controller forgets an explicit ->where().
 * - Unauthenticated context (console, queued jobs): not restricted here —
 *   those callers already know which university_id they operate on and
 *   scope explicitly; the risk this guards against is a logged-in
 *   university admin's request leaking across tenants.
 */
class UniversityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return;
        }

        if ($user->university_id) {
            $builder->where($model->getTable().'.university_id', $user->university_id);
        }
    }
}
