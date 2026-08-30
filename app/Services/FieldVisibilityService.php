<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyFieldPermission;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;

/**
 * Enforces the "minimum necessary data" principle (cahier des charges §21):
 * a company account does not automatically see every academic field just
 * because it is authenticated. Visibility is resolved field-by-field, with
 * a company-specific override taking precedence over the global default.
 */
class FieldVisibilityService
{
    /**
     * Champs consultables par une entreprise, avec leur valeur par défaut
     * (visible ou non) tant qu'aucune règle spécifique n'est définie.
     */
    private const DEFAULTS = [
        'full_name' => true,
        'unique_student_id' => true,
        'university_name' => true,
        'faculty_name' => true,
        'department_name' => true,
        'promotion_name' => true,
        'academic_year' => true,
        'degree_name' => true,
        'graduation_date' => true,
        'status' => true,
        'mention' => false,
        'average' => false,
        'birth_date' => false,
        'student_number' => false,
        'photo_path' => false,
    ];

    public function visibleFields(?Company $company): array
    {
        $overrides = Cache::remember(
            $this->cacheKey($company),
            now()->addMinutes(10),
            fn () => CompanyFieldPermission::query()
                ->where('company_id', $company?->id)
                ->pluck('is_visible', 'field_name')
                ->all()
        );

        return array_merge(self::DEFAULTS, $overrides);
    }

    /**
     * Reduces a fully-loaded student (+ its most relevant academic record)
     * down to the fields a given company is allowed to see. Never pass a
     * raw Eloquent model back through an API/controller response for a
     * company audience — always go through this method first.
     */
    public function filterStudentProfile(?Company $company, Student $student, ?\App\Models\StudentAcademicRecord $record = null): array
    {
        $visible = $this->visibleFields($company);

        $full = [
            'full_name' => $student->fullName(),
            'unique_student_id' => $student->unique_student_id,
            'student_number' => $student->student_number,
            'university_name' => $student->university?->name,
            'faculty_name' => $student->faculty?->name,
            'department_name' => $student->department?->name,
            'promotion_name' => $record?->promotion?->name,
            'academic_year' => $record?->academicYear?->label,
            'degree_name' => $record?->degree?->name,
            'graduation_date' => $record?->graduation_date?->toDateString(),
            'mention' => $record?->mention,
            'average' => $record?->average,
            'status' => $student->status,
            'birth_date' => $student->birth_date?->toDateString(),
            'photo_path' => $student->photo_path,
        ];

        return collect($full)
            ->filter(fn ($value, $field) => $visible[$field] ?? false)
            ->all();
    }

    private function cacheKey(?Company $company): string
    {
        return 'company_field_permissions:'.($company?->id ?? 'default');
    }
}
