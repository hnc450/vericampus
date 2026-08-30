<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Policies\Concerns\AuthorizesWithinUniversity;

class StudentPolicy
{
    use AuthorizesWithinUniversity;

    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isUniversityAdmin();
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isCompanyUser()) {
            // Les entreprises ne consultent jamais la fiche brute : elles passent
            // par AcademicVerificationService + FieldVisibilityService.
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->student_id === $student->id) {
            return true;
        }

        return $this->belongsToUsersUniversity($user, $student->university_id);
    }

    public function create(User $user): bool
    {
        return $user->isUniversityAdmin() || $user->isSuperAdmin();
    }

    public function update(User $user, Student $student): bool
    {
        return $this->belongsToUsersUniversity($user, $student->university_id);
    }

    public function deactivate(User $user, Student $student): bool
    {
        return $this->belongsToUsersUniversity($user, $student->university_id);
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->belongsToUsersUniversity($user, $student->university_id);
    }
}
