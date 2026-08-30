<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scopes\UniversityScope;
use App\Models\Student;
use App\Models\University;
use App\Services\AcademicVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function __construct(private readonly AcademicVerificationService $verification) {}

    public function index(): JsonResponse
    {
        $universities = University::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'short_name', 'city', 'country']);

        return response()->json(['data' => $universities]);
    }

    public function students(Request $request, string $university): JsonResponse
    {
        $request->user()->tokenCan('search') || abort(403);

        $data = $request->validate([
            'term' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        University::where('id', $university)->where('status', 'active')->firstOrFail();

        $results = Student::withoutGlobalScope(UniversityScope::class)
            ->where('university_id', $university)
            ->search($data['term'])
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $results->map(fn (Student $s) => $this->verification->toResultSummary($s)),
        ]);
    }
}
