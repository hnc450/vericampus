<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\Scopes\UniversityScope;
use App\Models\University;
use App\Services\AcademicVerificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(private readonly AcademicVerificationService $verification) {}

    public function create(): View
    {
        $universities = University::where('status', 'active')->orderBy('name')->get();

        return view('company.search', ['universities' => $universities, 'results' => null]);
    }

    /**
     * Recherche détaillée : Université → Année académique → Faculté → Nom
     * (cahier des charges §7). Une seule correspondance n'est jamais traitée
     * comme une identification automatique — la liste reste à désambiguïser
     * par l'entreprise avant toute consultation de fiche.
     */
    public function detailed(Request $request): View
    {
        $data = $request->validate([
            // Une entreprise ne peut jamais interroger une université suspendue,
            // même en forgeant l'identifiant dans la requête (§17).
            'university_id' => ['required', Rule::exists('universities', 'id')->where('status', 'active')],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'faculty_id' => ['nullable', 'integer', 'exists:faculties,id'],
            'name' => ['nullable', 'string', 'max:100'],
        ]);

        $company = $request->user()->company;

        $results = $this->verification->detailedSearch(
            $data['university_id'],
            $data['academic_year_id'] ?? null,
            $data['faculty_id'] ?? null,
            $data['name'] ?? null,
        );

        $this->verification->logSearch($company, $request->user(), $data['university_id'], $data);

        $universities = University::where('status', 'active')->orderBy('name')->get();
        $faculties = Faculty::withoutGlobalScope(UniversityScope::class)->where('university_id', $data['university_id'])->orderBy('name')->get();
        $academicYears = AcademicYear::withoutGlobalScope(UniversityScope::class)->where('university_id', $data['university_id'])->orderByDesc('start_date')->get();

        return view('company.search', [
            'universities' => $universities,
            'faculties' => $faculties,
            'academicYears' => $academicYears,
            'selected' => $data,
            'results' => $results,
        ]);
    }

    /**
     * Recherche rapide : Université + terme libre (nom / prénom / matricule).
     */
    public function quick(Request $request): View
    {
        $data = $request->validate([
            // Une entreprise ne peut jamais interroger une université suspendue,
            // même en forgeant l'identifiant dans la requête (§17).
            'university_id' => ['required', Rule::exists('universities', 'id')->where('status', 'active')],
            'term' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $company = $request->user()->company;

        $results = $this->verification->quickSearch($data['university_id'], $data['term']);

        $this->verification->logSearch($company, $request->user(), $data['university_id'], $data);

        $universities = University::where('status', 'active')->orderBy('name')->get();

        return view('company.search', [
            'universities' => $universities,
            'quickTerm' => $data['term'],
            'selected' => $data,
            'results' => $results,
        ]);
    }
}
