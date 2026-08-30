@extends('layouts.app')

@section('title', 'Fiche candidat')

@section('content')
    <h1 class="text-2xl font-semibold mb-1">{{ $profile['full_name'] ?? 'Candidat' }}</h1>
    <p class="text-sm text-emerald-700 mb-6">✓ Identité académique vérifiée</p>

    <div class="bg-white border border-slate-200 rounded-lg p-5 mb-8 text-sm space-y-2 max-w-xl">
        @foreach([
            'unique_student_id' => 'ID étudiant',
            'university_name' => 'Université',
            'faculty_name' => 'Faculté',
            'department_name' => 'Département',
            'promotion_name' => 'Promotion',
            'academic_year' => 'Année académique',
            'degree_name' => 'Diplôme',
            'status' => 'Statut',
        ] as $key => $label)
            @if(array_key_exists($key, $profile))
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">{{ $label }}</span>
                    <span class="font-medium">{{ $profile[$key] }}</span>
                </div>
            @endif
        @endforeach
        <p class="text-xs text-slate-400 pt-2">
            Seules les informations autorisées par la politique de confidentialité de votre compte entreprise sont affichées.
        </p>
    </div>

    @if($records->isNotEmpty())
        <h2 class="text-lg font-semibold mb-3">Générer un document de vérification</h2>
        <div class="bg-white border border-slate-200 rounded-lg p-5 max-w-xl space-y-3">
            @foreach($records as $record)
                <form method="POST" action="{{ route('company.verification.generate') }}" class="flex items-center justify-between border-b border-slate-100 pb-3 last:border-b-0 last:pb-0">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $studentId }}">
                    <input type="hidden" name="academic_record_id" value="{{ $record->id }}">
                    <span class="text-sm">{{ $record->academicYear->label }} — {{ $record->promotion?->name }}</span>
                    <button class="bg-slate-900 text-white text-xs px-3 py-1.5 rounded-md">Générer le document</button>
                </form>
            @endforeach
        </div>
    @else
        <p class="text-sm text-slate-400">Aucun dossier académique validé n'est disponible pour générer un document.</p>
    @endif
@endsection
