<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\DigitalSignature;
use App\Services\SignatureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DigitalSignatureController extends Controller
{
    public function __construct(private readonly SignatureService $signatures) {}

    public function edit(Request $request): View
    {
        $university = $request->user()->university;
        $history = $university->digitalSignatures()->latest('valid_from')->get();

        return view('university.signature.edit', [
            'university' => $university,
            'active' => $university->activeSignature(),
            'history' => $history,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $university = $request->user()->university;
        $this->authorize('create', DigitalSignature::class);

        $data = $request->validate([
            'authority_name' => ['required', 'string', 'max:150'],
            'authority_title' => ['required', 'string', 'max:150'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after:valid_from'],
            // Fichier de signature : image uniquement, taille strictement limitée.
            'signature_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ]);

        $this->signatures->create($university, $data, $request->file('signature_image'));

        return back()->with('status', "Nouvelle autorité signataire enregistrée : {$data['authority_name']}.");
    }

    public function revoke(DigitalSignature $signature): RedirectResponse
    {
        $this->authorize('manage', $signature);

        $this->signatures->revoke($signature);

        return back()->with('status', 'Signature révoquée.');
    }
}
