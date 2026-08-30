@extends('layouts.app')

@section('title', $student->fullName())

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $student->fullName() }}</h1>
            <div class="text-sm text-slate-500 font-mono">{{ $student->unique_student_id }}</div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('university.students.edit', $student) }}" class="border border-slate-300 text-sm px-4 py-2 rounded-md">Modifier</a>
            @if($student->status !== 'suspended')
                <form method="POST" action="{{ route('university.students.deactivate', $student) }}" onsubmit="return confirm('Désactiver cet étudiant ?');">
                    @csrf
                    <button class="border border-red-300 text-red-700 text-sm px-4 py-2 rounded-md">Désactiver</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="bg-white border border-slate-200 rounded-lg p-5 text-sm space-y-2">
            <div><span class="text-slate-500">Matricule</span> — {{ $student->student_number }}</div>
            <div><span class="text-slate-500">Faculté</span> — {{ $student->faculty?->name }}</div>
            <div><span class="text-slate-500">Département</span> — {{ $student->department?->name ?? '—' }}</div>
            <div><span class="text-slate-500">Statut</span> — {{ $student->status }}</div>
            <div><span class="text-slate-500">Date de naissance</span> — {{ optional($student->birth_date)->format('d/m/Y') ?? '—' }}</div>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-3">Dossiers académiques</h2>
    <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
        @forelse($student->academicRecords as $record)
            <div class="px-4 py-3 text-sm flex justify-between items-center">
                <span>
                    {{ $record->academicYear->label }} — {{ $record->promotion?->name }}
                    @if($record->degree) — {{ $record->degree->name }} @endif
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $record->status }}</span>
            </div>
        @empty
            <div class="px-4 py-6 text-sm text-slate-400 text-center">Aucun dossier académique importé pour cet étudiant.</div>
        @endforelse
    </div>
@endsection
