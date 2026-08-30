<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'company_id',
        'user_id',
        'university_id',
        'student_id',
        'verification_document_id',
        'type',
        'search_query',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'search_query' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function verificationDocument(): BelongsTo
    {
        return $this->belongsTo(VerificationDocument::class);
    }
}
