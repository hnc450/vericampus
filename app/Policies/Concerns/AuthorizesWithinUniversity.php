<?php

namespace App\Policies\Concerns;

use App\Models\User;

/**
 * Shared "same tenant" check for policies guarding models that carry a
 * university_id. The Eloquent global scope (UniversityScope) already keeps
 * cross-tenant rows out of query results, but a policy check is the layer
 * that actually decides whether an action is allowed once a model instance
 * is in hand (e.g. resolved via a signed/public route without auth scoping).
 */
trait AuthorizesWithinUniversity
{
    protected function belongsToUsersUniversity(User $user, string $universityId): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isUniversityAdmin() && $user->university_id === $universityId;
    }
}
