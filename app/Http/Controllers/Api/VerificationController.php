<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scopes\UniversityScope;
use App\Models\Student;
use App\Models\StudentAcademicRecord;
use App\Services\VerificationDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(private readonly VerificationDocumentService $documents) {}

    public function status(string $token): JsonResponse
    {
        $document = $this->documents->resolveByToken($token);

        if (! $document) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => $document->currentStatus(),
            'document_uid' => $document->document_uid,
            'university' => $document->university->name,
            'generated_at' => $document->created_at->toIso8601String(),
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $request->user()->tokenCan('verify') || abort(403);

        $data = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,id'],
            'academic_record_id' => ['required', 'integer', 'exists:student_academic_records,id'],
        ]);

        $student = Student::withoutGlobalScope(UniversityScope::class)->findOrFail($data['student_id']);
        $record = StudentAcademicRecord::where('id', $data['academic_record_id'])
            ->where('student_id', $student->id)
            ->where('status', 'validated')
            ->firstOrFail();

        $document = $this->documents->generate($student, $record, $request->user()->company, $request->user());

        return response()->json([
            'document_uid' => $document->document_uid,
            'verify_url' => route('public.verify', ['token' => $document->qr_token]),
        ], 201);
    }
}
