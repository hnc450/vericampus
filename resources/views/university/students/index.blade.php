@extends('layouts.app')

@section('title', 'Étudiants')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Étudiants</h1>
        <a href="{{ route('university.students.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md">Ajouter un étudiant</a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, prénom ou matricule…"
               class="border border-slate-300 rounded-md px-3 py-2 text-sm w-64">
        <select name="faculty_id" class="border border-slate-300 rounded-md px-3 py-2 text-sm">
            <option value="">Toutes les facultés</option>
            @foreach($faculties as $faculty)
                <option value="{{ $faculty->id }}" @selected(request('faculty_id') == $faculty->id)>{{ $faculty->name }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-slate-300 rounded-md px-3 py-2 text-sm">
            <option value="">Tous les statuts</option>
            @foreach(['active' => 'Actif', 'graduated' => 'Diplômé', 'dropped' => 'Abandon', 'suspended' => 'Suspendu'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="bg-slate-100 border border-slate-300 rounded-md px-4 py-2 text-sm">Filtrer</button>
    </form>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">ID étudiant</th>
                    <th class="px-4 py-3">Nom complet</th>
                    <th class="px-4 py-3">Matricule</th>
                    <th class="px-4 py-3">Faculté</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($students as $student)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $student->unique_student_id }}</td>
                        <td class="px-4 py-3">{{ $student->fullName() }}</td>
                        <td class="px-4 py-3">{{ $student->student_number }}</td>
                        <td class="px-4 py-3">{{ $student->faculty?->name }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $student->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('university.students.show', $student) }}" class="text-slate-700 hover:underline">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Aucun étudiant.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $students->links() }}</div>
@endsection
