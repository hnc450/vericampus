<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUniversity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VerificationDocument extends Model
{
    use BelongsToUniversity, HasFactory, HasUlids;

    protected $fillable = [
        'document_uid',
        'qr_token',
        'student_id',
        'university_id',
        'faculty_id',
        'academic_year_id',
        'promotion_id',
        'degree_id',
        'signature_id',
        'generated_by_company_id',
        'generated_by_user_id',
        'pdf_path',
        'status',
        'revoked_at',
        'revoked_reason',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'revoked_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function signature(): BelongsTo
    {
        return $this->belongsTo(DigitalSignature::class, 'signature_id');
    }

    public function generatedByCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'generated_by_company_id');
    }

    public function generatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(VerificationLog::class);
    }

    /**
     * Live status resolution for the public /verify page — never trust the
     * stored "status" column alone without also checking expiry, so a
     * document that quietly passed its expiry date still reads as invalid.
     */
    public function currentStatus(): string
    {
        if ($this->status === 'revoked') {
            return 'revoked';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        return 'verified';
    }

    public function isAuthentic(): bool
    {
        return $this->currentStatus() === 'verified';
    }
}
