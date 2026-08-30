<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Verification\GenerateDocumentRequest;
use App\Models\Scopes\UniversityScope;
use App\Models\Student;
use App\Models\StudentAcademicRecord;
use App\Services\AcademicVerificationService;
use App\Services\VerificationDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerificationController extends Controller
{
    public function __construct(
        private readonly AcademicVerificationService $verification,
        private readonly VerificationDocumentService $documents,
    ) {}

    public function show(Request $request, string $student): View
    {
        $profile = $this->verification->viewCandidate($request->user()->company, $request->user(), $student);

        $records = StudentAcademicRecord::where('student_id', $student)
            ->where('status', 'validated')
            ->with(['academicYear', 'promotion', 'degree'])
            ->get();

        return view('company.candidate', [
            'studentId' => $student,
            'profile' => $profile,
            'records' => $records,
        ]);
    }

    public function generate(GenerateDocumentRequest $request): RedirectResponse
    {
        $student = Student::withoutGlobalScope(UniversityScope::class)->findOrFail($request->validated('student_id'));
        $record = StudentAcademicRecord::where('id', $request->validated('academic_record_id'))
            ->where('student_id', $student->id)
            ->where('status', 'validated')
            ->firstOrFail();

        $document = $this->documents->generate($student, $record, $request->user()->company, $request->user());

        return redirect()->route('company.verification.download', $document)
            ->with('status', 'Document de vérification généré.');
    }

    public function download(\App\Models\VerificationDocument $document): StreamedResponse
    {
        $this->authorize('view', $document);

        return Storage::disk('local')->download($document->pdf_path, "verification-{$document->document_uid}.pdf");
    }
}
