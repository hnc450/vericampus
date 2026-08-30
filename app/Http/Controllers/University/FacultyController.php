<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Http\Requests\Faculty\StoreFacultyRequest;
use App\Models\Faculty;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function index(): View
    {
        $this->authorize('viewAny', Faculty::class);

        $faculties = Faculty::withCount(['students', 'departments'])
            ->orderBy('name')
            ->paginate(20);

        return view('university.faculties.index', compact('faculties'));
    }

    public function store(StoreFacultyRequest $request): RedirectResponse
    {
        $faculty = Faculty::create($request->validated());

        $this->auditLog->record('faculty.created', $faculty, [], $request->validated());

        return back()->with('status', "Faculté « {$faculty->name} » créée.");
    }

    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $this->authorize('update', $faculty);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $old = $faculty->only(array_keys($data));
        $faculty->update($data);

        $this->auditLog->record('faculty.updated', $faculty, $old, $data);

        return back()->with('status', 'Faculté mise à jour.');
    }
}
