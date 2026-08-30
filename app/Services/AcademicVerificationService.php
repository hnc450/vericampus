<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Scopes\UniversityScope;
use App\Models\Student;
use App\Models\StudentAcademicRecord;
use App\Models\User;
use App\Models\VerificationLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Search surface used by company accounts. A name match alone is never
 * treated as proof of identity (cahier des charges §22): every search
 * returns a list to disambiguate, and only viewCandidate() — logged and
 * filtered through FieldVisibilityService — reveals a fuller profile.
 */
class AcademicVerificationService
{
    public function __construct(private readonly FieldVisibilityService $fieldVisibility) {}

    public function detailedSearch(
        string $universityId,
        ?int $academicYearId,
        ?int $facultyId,
        ?string $name,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Student::withoutGlobalScope(UniversityScope::class)
            ->where('university_id', $universityId);

        if ($facultyId) {
            $query->where('faculty_id', $facultyId);
        }

        if ($academicYearId) {
            $query->whereHas('academicRecords', fn ($q) => $q->where('academic_year_id', $academicYearId));
        }

        if ($name) {
            $query->search($name);
        }

        return $query->with(['faculty', 'department'])
            ->orderBy('last_name')
            ->paginate($perPage);
    }

    public function quickSearch(string $universityId, string $term, int $perPage = 15): LengthAwarePaginator
    {
        return Student::withoutGlobalScope(UniversityScope::class)
            ->where('university_id', $universityId)
            ->search($term)
            ->with(['faculty'])
            ->orderBy('last_name')
            ->paginate($perPage);
    }

    /**
     * Minimal fields shown in a result list — enough to tell homonyms apart
     * without exposing a full profile before a specific candidate is chosen.
     */
    public function toResultSummary(Student $student): array
    {
        return [
            'id' => $student->id,
            'full_name' => $student->fullName(),
            'unique_student_id' => $student->unique_student_id,
            'faculty' => $student->faculty?->name,
            'status' => $student->status,
        ];
    }

    public function viewCandidate(?Company $company, ?User $user, string $studentId): array
    {
        $student = Student::withoutGlobalScope(UniversityScope::class)
            ->with(['university', 'faculty', 'department'])
            ->findOrFail($studentId);

        $latestRecord = StudentAcademicRecord::where('student_id', $student->id)
            ->where('status', 'validated')
            ->with(['promotion', 'academicYear', 'degree'])
            ->latest('graduation_date')
            ->first();

        $this->log('view', $company, $user, $student->university_id, $student->id, null, [
            'student_id' => $student->id,
        ]);

        return $this->fieldVisibility->filterStudentProfile($company, $student, $latestRecord);
    }

    public function logSearch(?Company $company, ?User $user, string $universityId, array $criteria): VerificationLog
    {
        return $this->log('search', $company, $user, $universityId, null, null, $criteria);
    }

    private function log(
        string $type,
        ?Company $company,
        ?User $user,
        ?string $universityId,
        ?string $studentId,
        ?string $documentId,
        array $query = [],
    ): VerificationLog {
        return VerificationLog::create([
            'company_id' => $company?->id,
            'user_id' => $user?->id,
            'university_id' => $universityId,
            'student_id' => $studentId,
            'verification_document_id' => $documentId,
            'type' => $type,
            'search_query' => $query ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
