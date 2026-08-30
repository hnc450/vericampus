<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * Single write path for every entry in the audit trail (cahier des charges §25).
 * Controllers and other services call record() instead of writing to the
 * audit_logs table directly, so the shape of an audit entry never drifts
 * between call sites.
 */
class AuditLogService
{
    public function record(
        string $action,
        ?Model $auditable = null,
        array $old = [],
        array $new = [],
        ?User $actor = null,
    ): AuditLog {
        $actor ??= auth()->user();

        return AuditLog::create([
            'user_id' => $actor?->id,
            'action' => $action,
            'auditable_type' => $auditable ? $auditable::class : null,
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => RequestFacade::ip(),
            'user_agent' => RequestFacade::userAgent(),
        ]);
    }
}
