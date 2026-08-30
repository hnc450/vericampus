<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function index(): View
    {
        $this->authorize('viewAny', Company::class);

        $companies = Company::withCount('users')->orderBy('name')->paginate(20);

        return view('superadmin.companies.index', compact('companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Company::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'sector' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email'],
        ]);

        $company = Company::create($data + ['status' => 'active', 'created_by' => $request->user()->id]);

        $this->auditLog->record('company.created', $company, [], $data);

        return back()->with('status', "Entreprise « {$company->name} » créée.");
    }

    public function suspend(Company $company): RedirectResponse
    {
        $this->authorize('suspend', $company);

        $company->update(['status' => 'suspended']);
        $this->auditLog->record('company.suspended', $company);

        return back()->with('status', 'Entreprise suspendue.');
    }
}
