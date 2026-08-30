<?php

namespace App\Policies;

use App\Models\University;
use App\Models\User;

class UniversityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isUniversityAdmin();
    }

    public function view(User $user, University $university): bool
    {
        return $user->isSuperAdmin() || $user->university_id === $university->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, University $university): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isUniversityAdmin() && $user->university_id === $university->id;
    }

    public function suspend(User $user, University $university): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, University $university): bool
    {
        return $user->isSuperAdmin();
    }
}
