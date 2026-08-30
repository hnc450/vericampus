<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\VerificationDocument;
use App\Services\VerificationDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationDocumentController extends Controller
{
    public function __construct(private readonly VerificationDocumentService $documents) {}

    public function index(Request $request): View
    {
        $documents = VerificationDocument::with(['student', 'generatedByCompany'])
            ->where('university_id', $request->user()->university_id)
            ->latest()
            ->paginate(20);

        return view('university.documents.index', compact('documents'));
    }

    public function revoke(Request $request, VerificationDocument $document): RedirectResponse
    {
        $this->authorize('revoke', $document);

        $data = $request->validate([
            'revoked_reason' => ['required', 'string', 'max:255'],
        ]);

        $this->documents->revoke($document, $data['revoked_reason']);

        return back()->with('status', 'Document révoqué.');
    }
}
