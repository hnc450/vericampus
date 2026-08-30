@extends('layouts.app')

@section('title', 'Rechercher un candidat')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Rechercher un candidat</h1>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="bg-white border border-slate-200 rounded-lg p-5">
            <h2 class="font-medium mb-3">Recherche rapide</h2>
            <form method="GET" action="{{ route('company.search.quick') }}" class="space-y-3">
                <select name="university_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                    <option value="">Université…</option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}" @selected(($selected['university_id'] ?? null) == $university->id)>{{ $university->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="term" value="{{ $quickTerm ?? '' }}" placeholder="Nom, prénom ou matricule…" minlength="2" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                <button class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md w-full">Rechercher</button>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-5">
            <h2 class="font-medium mb-3">Recherche détaillée</h2>
            <form method="GET" action="{{ route('company.search.detailed') }}" class="space-y-3">
                <select name="university_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                    <option value="">Université…</option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}" @selected(($selected['university_id'] ?? null) == $university->id)>{{ $university->name }}</option>
                    @endforeach
                </select>
                @if(isset($academicYears))
                    <select name="academic_year_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
                        <option value="">Toutes les années</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(($selected['academic_year_id'] ?? null) == $year->id)>{{ $year->label }}</option>
                        @endforeach
                    </select>
                @endif
                @if(isset($faculties))
                    <select name="faculty_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
                        <option value="">Toutes les facultés</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" @selected(($selected['faculty_id'] ?? null) == $faculty->id)>{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                @endif
                <input type="text" name="name" value="{{ $selected['name'] ?? '' }}" placeholder="Nom du candidat" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
                <button class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md w-full">Rechercher</button>
            </form>
        </div>
    </div>

    @if($results)
        <h2 class="text-lg font-semibold mb-3">Résultats</h2>
        <p class="text-xs text-slate-500 mb-3">
            Plusieurs candidats peuvent porter le même nom — vérifiez l'ID étudiant et la faculté avant de sélectionner.
        </p>
        <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
            @forelse($results as $student)
                <div class="px-4 py-3 text-sm flex justify-between items-center">
                    <span>
                        <strong>{{ $student->fullName() }}</strong>
                        <span class="text-slate-400 font-mono text-xs ml-2">{{ $student->unique_student_id }}</span>
                        <span class="text-slate-400 text-xs ml-2">{{ $student->faculty?->name }}</span>
                    </span>
                    <a href="{{ route('company.candidate.show', $student->id) }}" class="text-slate-700 hover:underline text-sm">Consulter</a>
                </div>
            @empty
                <div class="px-4 py-6 text-sm text-slate-400 text-center">Aucun candidat trouvé.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $results->links() }}</div>
    @endif
@endsection
