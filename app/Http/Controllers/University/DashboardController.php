<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\VerificationDocument;
use App\Models\VerificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $universityId = $request->user()->university_id;

        $stats = [
            'students' => Student::count(),
            'graduates' => Student::where('status', 'graduated')->count(),
            'faculties' => Faculty::count(),
            'academic_years' => AcademicYear::count(),
            'documents_generated' => VerificationDocument::where('university_id', $universityId)->count(),
            'verifications' => VerificationLog::where('university_id', $universityId)->count(),
        ];

        $recentLogs = VerificationLog::where('university_id', $universityId)
            ->with(['company', 'student'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('university.dashboard', compact('stats', 'recentLogs'));
    }
}
