<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\VerificationDocumentService;
use Illuminate\View\View;

class PublicVerificationController extends Controller
{
    public function __construct(private readonly VerificationDocumentService $documents) {}

    /**
     * Page publique atteinte en scannant le QR Code (cahier des charges §10).
     * Le statut affiché est toujours recalculé en direct — jamais mis en
     * cache — pour qu'un document révoqué après coup ne s'affiche jamais
     * comme authentique.
     */
    public function show(string $token): View
    {
        $document = $this->documents->resolveByToken($token);

        if (! $document) {
            return view('public.verify', ['found' => false]);
        }

        return view('public.verify', [
            'found' => true,
            'status' => $document->currentStatus(),
            'document' => $document,
        ]);
    }
}
