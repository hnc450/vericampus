<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUniversity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use BelongsToUniversity, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'university_id',
        'faculty_id',
        'department_id',
        'student_number',
        'unique_student_id',
        'last_name',
        'middle_name',
        'first_name',
        'sex',
        'birth_date',
        'photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function userAccount(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function academicRecords(): HasMany
    {
        return $this->hasMany(StudentAcademicRecord::class);
    }

    public function verificationDocuments(): HasMany
    {
        return $this->hasMany(VerificationDocument::class);
    }

    public function fullName(): string
    {
        return trim("{$this->last_name} {$this->middle_name} {$this->first_name}");
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term) {
            $q->whereRaw('LOWER(last_name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(first_name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(middle_name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(student_number) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(unique_student_id) LIKE ?', ["%{$term}%"]);
        });
    }

    public static function generateUniqueStudentId(University $university): string
    {
        $year = now()->year;
        $prefix = mb_strtoupper($university->short_name ?: 'UNI');

        do {
            $sequence = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            $candidate = "{$prefix}-{$year}-{$sequence}";
        } while (static::withoutGlobalScopes()->where('unique_student_id', $candidate)->exists());

        return $candidate;
    }
}
