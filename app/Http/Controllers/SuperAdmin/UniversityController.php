<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function index(): View
    {
        $this->authorize('viewAny', University::class);

        $universities = University::withCount('students')->orderBy('name')->paginate(20);

        return view('superadmin.universities.index', compact('universities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', University::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'short_name' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $university = University::create($data + ['status' => 'active', 'created_by' => $request->user()->id]);

        $this->auditLog->record('university.created', $university, [], $data);

        return back()->with('status', "Université « {$university->name} » créée.");
    }

    public function suspend(University $university): RedirectResponse
    {
        $this->authorize('suspend', $university);

        $university->update(['status' => 'suspended']);
        $this->auditLog->record('university.suspended', $university);

        return back()->with('status', 'Université suspendue.');
    }

    public function reactivate(University $university): RedirectResponse
    {
        $this->authorize('suspend', $university);

        $university->update(['status' => 'active']);
        $this->auditLog->record('university.reactivated', $university);

        return back()->with('status', 'Université réactivée.');
    }
}
