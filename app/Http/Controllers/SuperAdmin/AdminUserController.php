<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\University;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function index(): View
    {
        $users = User::with(['university', 'company'])
            ->whereIn('id', fn ($q) => $q->select('model_id')->from('model_has_roles'))
            ->orderBy('name')
            ->paginate(20);

        $universities = University::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('superadmin.admin-users.index', compact('users', 'universities', 'companies'));
    }

    /**
     * Crée un compte administrateur (université) ou compte entreprise et lui
     * envoie un lien de définition de mot de passe — jamais de mot de passe
     * en clair transmis par ce formulaire.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['university_admin', 'company_user'])],
            'university_id' => ['required_if:role,university_admin', 'nullable', 'exists:universities,id'],
            'company_id' => ['required_if:role,company_user', 'nullable', 'exists:companies,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'university_id' => $data['role'] === 'university_admin' ? $data['university_id'] : null,
            'company_id' => $data['role'] === 'company_user' ? $data['company_id'] : null,
            'status' => 'active',
        ]);

        $user->assignRole($data['role']);

        event(new Registered($user));

        $this->auditLog->record('admin_user.created', $user, [], ['role' => $data['role']]);

        return back()->with('status', "Compte créé pour {$user->name} — un email de définition de mot de passe a été envoyé.");
    }

    public function suspend(User $user): RedirectResponse
    {
        $user->update(['status' => 'suspended']);
        $this->auditLog->record('admin_user.suspended', $user);

        return back()->with('status', 'Compte suspendu.');
    }
}
