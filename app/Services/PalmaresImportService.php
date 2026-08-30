<?php

namespace App\Services;

use App\Imports\PalmaresRowsImport;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\PalmaresImport;
use App\Models\PalmaresRow;
use App\Models\Promotion;
use App\Models\Scopes\UniversityScope;
use App\Models\Student;
use App\Models\StudentAcademicRecord;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Palmarès import, in two phases (cahier des charges §6):
 *   1. preview()  — read-only, shows what WOULD happen, never touches the DB.
 *   2. store() + process() — persists the upload, then a queued Job commits
 *      it row by row so a 5000-line file never blocks the HTTP request.
 *
 * An import never deletes or silently overwrites existing student records —
 * a matricule already on file is reported as a duplicate, not merged.
 */
class PalmaresImportService
{
    private const REQUIRED_COLUMNS = ['matricule', 'nom', 'prenom'];

    public function __construct(private readonly AuditLogService $auditLog) {}

    public function preview(UploadedFile $file, Faculty $faculty): array
    {
        $rows = $this->readRows($file);
        $seen = [];
        $preview = [];

        foreach ($rows as $index => $row) {
            $evaluation = $this->evaluateRow($row, $faculty->university_id, $seen);
            $preview[] = array_merge(['row_number' => $index + 2, 'data' => $row->toArray()], $evaluation);
        }

        return [
            'total' => count($preview),
            'would_import' => collect($preview)->where('status', 'imported')->count(),
            'would_reject' => collect($preview)->where('status', 'rejected')->count(),
            'duplicates' => collect($preview)->where('status', 'duplicate')->count(),
            'rows' => $preview,
        ];
    }

    public function store(
        UploadedFile $file,
        Faculty $faculty,
        AcademicYear $academicYear,
        Promotion $promotion,
        User $uploadedBy,
    ): PalmaresImport {
        $university = $faculty->university;
        $storedPath = $file->store('palmares-imports/'.$university->id);

        return PalmaresImport::create([
            'university_id' => $university->id,
            'faculty_id' => $faculty->id,
            'academic_year_id' => $academicYear->id,
            'promotion_id' => $promotion->id,
            'uploaded_by' => $uploadedBy->id,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'status' => 'pending',
        ]);
    }

    /**
     * Runs inside App\Jobs\ProcessPalmaresImport. Reads the file back from
     * disk (not from the original UploadedFile, which does not survive a
     * queue round-trip) and commits row by row inside a transaction.
     */
    public function process(PalmaresImport $import): void
    {
        $import->update(['status' => 'processing']);

        try {
            $rows = $this->readStoredRows($import->file_path);
        } catch (\Throwable $e) {
            $import->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);

            return;
        }

        $university = University::withoutGlobalScope(UniversityScope::class)->findOrFail($import->university_id);
        $imported = 0;
        $rejected = 0;
        $duplicates = 0;
        $seen = [];

        DB::transaction(function () use ($rows, $import, $university, &$imported, &$rejected, &$duplicates, &$seen) {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $evaluation = $this->evaluateRow($row, $university->id, $seen);

                if ($evaluation['status'] === 'rejected') {
                    $this->recordRow($import, $rowNumber, $row, 'rejected', null, $evaluation['error']);
                    $rejected++;

                    continue;
                }

                $matricule = trim((string) $row['matricule']);
                $student = Student::withoutGlobalScope(UniversityScope::class)
                    ->where('university_id', $university->id)
                    ->where('student_number', $matricule)
                    ->first();

                if (! $student) {
                    $student = Student::create([
                        'university_id' => $university->id,
                        'faculty_id' => $import->faculty_id,
                        'student_number' => $matricule,
                        'unique_student_id' => Student::generateUniqueStudentId($university),
                        'last_name' => trim((string) $row['nom']),
                        'middle_name' => trim((string) ($row['postnom'] ?? '')) ?: null,
                        'first_name' => trim((string) $row['prenom']),
                        'sex' => $this->normalizeSex($row['sexe'] ?? null),
                        'birth_date' => $this->normalizeDate($row['date_naissance'] ?? null),
                        'status' => 'active',
                    ]);
                }

                $existingRecord = StudentAcademicRecord::where('student_id', $student->id)
                    ->where('academic_year_id', $import->academic_year_id)
                    ->exists();

                if ($existingRecord) {
                    $this->recordRow($import, $rowNumber, $row, 'duplicate', $student->id, 'Résultat déjà enregistré pour cette année académique.');
                    $duplicates++;

                    continue;
                }

                StudentAcademicRecord::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $import->academic_year_id,
                    'promotion_id' => $import->promotion_id,
                    'mention' => $row['mention'] ?? null,
                    'average' => is_numeric($row['moyenne'] ?? null) ? $row['moyenne'] : null,
                    'status' => 'pending',
                    'palmares_import_id' => $import->id,
                ]);

                $this->recordRow($import, $rowNumber, $row, 'imported', $student->id);
                $imported++;
            }
        });

        $import->update([
            'status' => 'completed',
            'total_rows' => $imported + $rejected + $duplicates,
            'imported_rows' => $imported,
            'rejected_rows' => $rejected,
            'duplicate_rows' => $duplicates,
            'report_path' => null,
        ]);

        $this->auditLog->record('palmares.imported', $import, [], [
            'imported' => $imported,
            'rejected' => $rejected,
            'duplicates' => $duplicates,
        ]);
    }

    private function evaluateRow(Collection $row, string $universityId, array &$seen): array
    {
        foreach (self::REQUIRED_COLUMNS as $column) {
            if (! trim((string) ($row[$column] ?? ''))) {
                return ['status' => 'rejected', 'error' => "Colonne obligatoire manquante : {$column}."];
            }
        }

        $matricule = trim((string) $row['matricule']);

        if (isset($seen[$matricule])) {
            return ['status' => 'duplicate', 'error' => 'Matricule en double dans le fichier importé.'];
        }
        $seen[$matricule] = true;

        $exists = Student::withoutGlobalScope(UniversityScope::class)
            ->where('university_id', $universityId)
            ->where('student_number', $matricule)
            ->exists();

        return $exists
            ? ['status' => 'duplicate', 'error' => 'Étudiant déjà présent — le résultat de cette année sera ignoré s\'il existe déjà.']
            : ['status' => 'imported', 'error' => null];
    }

    private function recordRow(PalmaresImport $import, int $rowNumber, Collection $row, string $status, ?string $studentId, ?string $error = null): void
    {
        PalmaresRow::create([
            'palmares_import_id' => $import->id,
            'row_number' => $rowNumber,
            'raw_data' => $row->toArray(),
            'status' => $status,
            'student_id' => $studentId,
            'error_message' => $error,
        ]);
    }

    private function readRows(UploadedFile $file): Collection
    {
        $reader = new PalmaresRowsImport;
        Excel::import($reader, $file);

        return $reader->rows;
    }

    private function readStoredRows(string $path): Collection
    {
        $reader = new PalmaresRowsImport;
        Excel::import($reader, $path);

        return $reader->rows;
    }

    private function normalizeSex(?string $value): ?string
    {
        $value = mb_strtoupper(trim((string) $value));

        return in_array($value, ['M', 'F'], true) ? $value : null;
    }

    private function normalizeDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
