<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AcademicVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private readonly AcademicVerificationService $verification) {}

    public function show(Request $request, string $student): JsonResponse
    {
        $request->user()->tokenCan('verify') || abort(403);

        $company = $request->user()->company;
        $profile = $this->verification->viewCandidate($company, $request->user(), $student);

        return response()->json(['data' => $profile]);
    }
}
