@extends('layouts.app')

@section('title', 'Facultés')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Facultés</h1>

    <form method="POST" action="{{ route('university.faculties.store') }}" class="bg-white border border-slate-200 rounded-lg p-5 mb-8 flex flex-wrap gap-3 items-end">
        @csrf
        <div>
            <label class="block text-xs font-medium mb-1">Nom</label>
            <input type="text" name="name" value="{{ old('name') }}" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-64" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Code</label>
            <input type="text" name="code" value="{{ old('code') }}" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-32" required>
        </div>
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-medium mb-1">Description</label>
            <input type="text" name="description" value="{{ old('description') }}" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-full">
        </div>
        <button class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md">Ajouter</button>
    </form>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Étudiants</th>
                    <th class="px-4 py-3">Départements</th>
                    <th class="px-4 py-3">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($faculties as $faculty)
                    <tr>
                        <td class="px-4 py-3">{{ $faculty->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $faculty->code }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $faculty->students_count }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $faculty->departments_count }}</td>
                        <td class="px-4 py-3">{{ $faculty->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Aucune faculté.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $faculties->links() }}</div>
@endsection
