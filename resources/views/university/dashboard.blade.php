@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Tableau de bord</h1>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
        @foreach([
            'Étudiants' => $stats['students'],
            'Diplômés' => $stats['graduates'],
            'Facultés' => $stats['faculties'],
            'Années académiques' => $stats['academic_years'],
            'Documents générés' => $stats['documents_generated'],
            'Vérifications effectuées' => $stats['verifications'],
        ] as $label => $value)
            <div class="bg-white rounded-lg border border-slate-200 p-5">
                <div class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</div>
                <div class="text-2xl font-semibold mt-1 tabular-nums">{{ number_format($value) }}</div>
            </div>
        @endforeach
    </div>

    <h2 class="text-lg font-semibold mb-3">Dernières activités</h2>
    <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
        @forelse($recentLogs as $log)
            <div class="px-4 py-3 text-sm flex justify-between">
                <span>
                    <strong>{{ ucfirst($log->type) }}</strong>
                    @if($log->company) — {{ $log->company->name }} @endif
                    @if($log->student) — {{ $log->student->fullName() }} @endif
                </span>
                <span class="text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
            </div>
        @empty
            <div class="px-4 py-6 text-sm text-slate-400 text-center">Aucune activité pour le moment.</div>
        @endforelse
    </div>
@endsection
