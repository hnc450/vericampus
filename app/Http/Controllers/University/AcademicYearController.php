<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYear\StoreAcademicYearRequest;
use App\Models\AcademicYear;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function index(): View
    {
        $years = AcademicYear::orderByDesc('start_date')->paginate(20);

        return view('university.academic-years.index', compact('years'));
    }

    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        $year = AcademicYear::create($request->validated() + ['status' => 'active']);

        $this->auditLog->record('academic_year.created', $year, [], $request->validated());

        return back()->with('status', "Année académique « {$year->label} » créée.");
    }

    public function close(AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('update', $academicYear->university);

        $academicYear->update(['status' => 'closed']);

        $this->auditLog->record('academic_year.closed', $academicYear);

        return back()->with('status', 'Année académique clôturée.');
    }
}
