<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalSignature extends Model
{
    use BelongsToUniversity, HasFactory;

    protected $fillable = [
        'university_id',
        'authority_name',
        'authority_title',
        'signature_image_path',
        'valid_from',
        'valid_until',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCurrentlyValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->valid_from->isFuture()) {
            return false;
        }

        return ! $this->valid_until || ! $this->valid_until->isPast();
    }
}
