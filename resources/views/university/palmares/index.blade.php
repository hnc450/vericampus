@extends('layouts.app')

@section('title', 'Palmarès')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Imports de palmarès</h1>
        <a href="{{ route('university.palmares.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md">Nouvel import</a>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Fichier</th>
                    <th class="px-4 py-3">Faculté</th>
                    <th class="px-4 py-3">Année</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3">Importées / Doublons / Rejetées</th>
                    <th class="px-4 py-3">Par</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($imports as $import)
                    <tr>
                        <td class="px-4 py-3">{{ $import->original_filename }}</td>
                        <td class="px-4 py-3">{{ $import->faculty->name }}</td>
                        <td class="px-4 py-3">{{ $import->academicYear->label }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $import->status }}</span>
                        </td>
                        <td class="px-4 py-3 tabular-nums">{{ $import->imported_rows }} / {{ $import->duplicate_rows }} / {{ $import->rejected_rows }}</td>
                        <td class="px-4 py-3">{{ $import->uploadedBy->name }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('university.palmares.show', $import) }}" class="hover:underline">Détail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Aucun import.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $imports->links() }}</div>
@endsection
