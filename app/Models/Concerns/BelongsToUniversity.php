<?php

namespace App\Models\Concerns;

use App\Models\Scopes\UniversityScope;
use App\Models\University;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Applied to every model that carries a direct university_id column.
 * Enforces tenant isolation at the query level via UniversityScope,
 * so a scoped query can never leak another university's rows even if
 * a controller forgets to filter explicitly.
 */
trait BelongsToUniversity
{
    public static function bootBelongsToUniversity(): void
    {
        static::addGlobalScope(new UniversityScope);

        static::creating(function ($model) {
            if (! $model->university_id && auth()->check() && auth()->user()->university_id) {
                $model->university_id = auth()->user()->university_id;
            }
        });
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
