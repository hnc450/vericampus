<?php

namespace App\Services;

use App\Models\DigitalSignature;
use App\Models\University;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Owns the lifecycle of a university's signing authority. The signature
 * image itself always lives on the private "signatures" disk (see
 * config/filesystems.php) — this service is the only place in the codebase
 * allowed to read that file, and it never returns a public URL for it.
 */
class SignatureService
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function create(University $university, array $data, UploadedFile $signatureImage): DigitalSignature
    {
        // Une seule signature active à la fois : l'arrivée d'une nouvelle
        // autorité (nouveau recteur) retire l'ancienne sans supprimer l'historique.
        $university->digitalSignatures()->where('status', 'active')->update(['status' => 'revoked']);

        $path = $signatureImage->store('signatures/'.$university->id, 'signatures');

        $signature = $university->digitalSignatures()->create([
            'authority_name' => $data['authority_name'],
            'authority_title' => $data['authority_title'],
            'signature_image_path' => $path,
            'valid_from' => $data['valid_from'] ?? now(),
            'valid_until' => $data['valid_until'] ?? null,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        $this->auditLog->record('signature.created', $signature, [], [
            'authority_name' => $signature->authority_name,
            'university_id' => $university->id,
        ]);

        return $signature;
    }

    public function revoke(DigitalSignature $signature): void
    {
        $signature->update(['status' => 'revoked']);

        $this->auditLog->record('signature.revoked', $signature);
    }

    /**
     * Server-side only: returns the raw image bytes for embedding into a
     * generated PDF. Never expose this through a controller response.
     */
    public function imageContents(DigitalSignature $signature): string
    {
        return Storage::disk('signatures')->get($signature->signature_image_path);
    }

    public function imageDataUri(DigitalSignature $signature): string
    {
        $contents = $this->imageContents($signature);
        $mime = Str::endsWith($signature->signature_image_path, '.png') ? 'image/png' : 'image/jpeg';

        return "data:{$mime};base64,".base64_encode($contents);
    }
}
