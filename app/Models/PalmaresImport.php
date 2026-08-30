<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PalmaresImport extends Model
{
    use BelongsToUniversity, HasFactory;

    protected $fillable = [
        'university_id',
        'faculty_id',
        'academic_year_id',
        'promotion_id',
        'uploaded_by',
        'original_filename',
        'file_path',
        'status',
        'total_rows',
        'imported_rows',
        'rejected_rows',
        'duplicate_rows',
        'report_path',
        'failure_reason',
    ];

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

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(PalmaresRow::class);
    }
}
