<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Student;
use App\Models\StudentAcademicRecord;
use App\Models\User;
use App\Models\VerificationDocument;
use App\Models\VerificationLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Composes a verification document: creates the record, renders the PDF
 * with the university's active signature and an opaque-token QR code, and
 * stores it. This is the only place in the app that touches a signature
 * image and a QR token in the same request — everywhere else works with
 * the resulting VerificationDocument model.
 */
class VerificationDocumentService
{
    public function __construct(
        private readonly QRCodeService $qrCodes,
        private readonly SignatureService $signatures,
        private readonly AuditLogService $auditLog,
    ) {}

    public function generate(
        Student $student,
        StudentAcademicRecord $record,
        ?Company $company = null,
        ?User $user = null,
    ): VerificationDocument {
        $university = $student->university;
        $signature = $university->activeSignature();

        $ttlDays = config('vericampus.document_ttl_days');

        $document = VerificationDocument::create([
            'document_uid' => (string) Str::uuid(),
            'qr_token' => $this->qrCodes->generateToken(),
            'student_id' => $student->id,
            'university_id' => $university->id,
            'faculty_id' => $student->faculty_id,
            'academic_year_id' => $record->academic_year_id,
            'promotion_id' => $record->promotion_id,
            'degree_id' => $record->degree_id,
            'signature_id' => $signature?->id,
            'generated_by_company_id' => $company?->id,
            'generated_by_user_id' => $user?->id,
            'status' => 'verified',
            'expires_at' => $ttlDays ? now()->addDays($ttlDays) : null,
        ]);

        $document->load(['student', 'university', 'faculty', 'academicYear', 'promotion', 'degree', 'signature']);

        $pdfPath = $this->storePdf($document);
        $document->update(['pdf_path' => $pdfPath]);

        $this->auditLog->record('verification_document.generated', $document, [], [
            'student_id' => $student->id,
            'university_id' => $university->id,
        ]);

        VerificationLog::create([
            'company_id' => $company?->id,
            'user_id' => $user?->id,
            'university_id' => $university->id,
            'student_id' => $student->id,
            'verification_document_id' => $document->id,
            'type' => 'generate_document',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        return $document;
    }

    public function revoke(VerificationDocument $document, string $reason): VerificationDocument
    {
        $document->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_reason' => $reason,
        ]);

        $this->auditLog->record('verification_document.revoked', $document, [], ['reason' => $reason]);

        return $document;
    }

    /**
     * Resolves a document from its public QR token and logs the scan. The
     * returned status always reflects live state (revoked/expired/verified)
     * — never a value cached at generation time (cahier des charges §24).
     */
    public function resolveByToken(string $qrToken): ?VerificationDocument
    {
        $document = VerificationDocument::withoutGlobalScopes()
            ->with(['student', 'university', 'faculty', 'academicYear'])
            ->where('qr_token', $qrToken)
            ->first();

        if ($document) {
            VerificationLog::create([
                'university_id' => $document->university_id,
                'student_id' => $document->student_id,
                'verification_document_id' => $document->id,
                'type' => 'qr_scan',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }

        return $document;
    }

    private function storePdf(VerificationDocument $document): string
    {
        $qrImage = $this->qrCodes->pngDataUriFor($document->qr_token);
        $signatureImage = $document->signature ? $this->signatures->imageDataUri($document->signature) : null;

        $pdf = Pdf::loadView('documents.verification', [
            'document' => $document,
            'qrImage' => $qrImage,
            'signatureImage' => $signatureImage,
        ])->setPaper('a4');

        $path = "verification-documents/{$document->university_id}/{$document->id}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }
}
