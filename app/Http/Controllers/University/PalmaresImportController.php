<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Http\Requests\Palmares\ImportPalmaresRequest;
use App\Jobs\ProcessPalmaresImport;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\PalmaresImport;
use App\Models\Promotion;
use App\Services\PalmaresImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PalmaresImportController extends Controller
{
    public function __construct(private readonly PalmaresImportService $imports) {}

    public function create(): View
    {
        $faculties = Faculty::with('promotions')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('university.palmares.import', compact('faculties', 'academicYears'));
    }

    /**
     * Étape de prévisualisation — ne touche jamais la base de données
     * (cahier des charges §6 : upload → lecture → aperçu → validation → import).
     */
    public function preview(Request $request): View
    {
        $request->validate([
            'faculty_id' => ['required', 'exists:faculties,id'],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
        ]);

        $faculty = Faculty::findOrFail($request->integer('faculty_id'));
        $preview = $this->imports->preview($request->file('file'), $faculty);

        return view('university.palmares.preview', [
            'preview' => $preview,
            'faculty' => $faculty,
        ]);
    }

    public function store(ImportPalmaresRequest $request): RedirectResponse
    {
        $faculty = Faculty::findOrFail($request->integer('faculty_id'));
        $academicYear = AcademicYear::findOrFail($request->integer('academic_year_id'));
        $promotion = Promotion::findOrFail($request->integer('promotion_id'));

        $import = $this->imports->store($request->file('file'), $faculty, $academicYear, $promotion, $request->user());

        ProcessPalmaresImport::dispatch($import);

        return redirect()->route('university.palmares.show', $import)
            ->with('status', 'Import lancé — le traitement se poursuit en arrière-plan.');
    }

    public function index(): View
    {
        $imports = PalmaresImport::with(['faculty', 'academicYear', 'uploadedBy'])
            ->latest()
            ->paginate(20);

        return view('university.palmares.index', compact('imports'));
    }

    public function show(PalmaresImport $palmares): View
    {
        $palmares->load(['faculty', 'academicYear', 'promotion', 'uploadedBy', 'rows' => fn ($q) => $q->where('status', '!=', 'imported')->limit(200)]);

        return view('university.palmares.show', ['import' => $palmares]);
    }
}
