<?php

namespace App\Policies;

use App\Models\Faculty;
use App\Models\User;
use App\Policies\Concerns\AuthorizesWithinUniversity;

class FacultyPolicy
{
    use AuthorizesWithinUniversity;

    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isUniversityAdmin();
    }

    public function view(User $user, Faculty $faculty): bool
    {
        return $this->belongsToUsersUniversity($user, $faculty->university_id);
    }

    public function create(User $user): bool
    {
        return $user->isUniversityAdmin() || $user->isSuperAdmin();
    }

    public function update(User $user, Faculty $faculty): bool
    {
        return $this->belongsToUsersUniversity($user, $faculty->university_id);
    }

    public function delete(User $user, Faculty $faculty): bool
    {
        return $this->belongsToUsersUniversity($user, $faculty->university_id);
    }
}
