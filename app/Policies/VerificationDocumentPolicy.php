<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VerificationDocument;
use App\Policies\Concerns\AuthorizesWithinUniversity;

class VerificationDocumentPolicy
{
    use AuthorizesWithinUniversity;

    public function view(User $user, VerificationDocument $document): bool
    {
        if ($user->isCompanyUser()) {
            return $user->company_id === $document->generated_by_company_id;
        }

        return $this->belongsToUsersUniversity($user, $document->university_id);
    }

    public function revoke(User $user, VerificationDocument $document): bool
    {
        return $this->belongsToUsersUniversity($user, $document->university_id);
    }
}
