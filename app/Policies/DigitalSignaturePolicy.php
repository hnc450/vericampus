<?php

namespace App\Policies;

use App\Models\DigitalSignature;
use App\Models\User;
use App\Policies\Concerns\AuthorizesWithinUniversity;

class DigitalSignaturePolicy
{
    use AuthorizesWithinUniversity;

    /**
     * The signature image itself is never served through a policy-gated
     * "view" — it is only ever read server-side by VerificationDocumentService
     * when composing a PDF. This policy only governs management actions.
     */
    public function manage(User $user, DigitalSignature $signature): bool
    {
        return $this->belongsToUsersUniversity($user, $signature->university_id);
    }

    public function create(User $user): bool
    {
        return $user->isUniversityAdmin() || $user->isSuperAdmin();
    }
}
