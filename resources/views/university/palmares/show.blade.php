@extends('layouts.app')

@section('title', 'Rapport d\'import')

@section('content')
    <h1 class="text-2xl font-semibold mb-2">Rapport d'import — {{ $import->original_filename }}</h1>
    <p class="text-sm text-slate-500 mb-6">
        {{ $import->faculty->name }} · {{ $import->academicYear->label }} · {{ $import->promotion->name }} ·
        importé par {{ $import->uploadedBy->name }} le {{ $import->created_at->format('d/m/Y à H:i') }}
    </p>

    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <div class="text-xs uppercase text-slate-500">Statut</div>
            <div class="text-lg font-semibold">{{ $import->status }}</div>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
            <div class="text-xs uppercase text-emerald-700">Importées</div>
            <div class="text-xl font-semibold tabular-nums text-emerald-800">{{ $import->imported_rows }}</div>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="text-xs uppercase text-amber-700">Doublons</div>
            <div class="text-xl font-semibold tabular-nums text-amber-800">{{ $import->duplicate_rows }}</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-xs uppercase text-red-700">Rejetées</div>
            <div class="text-xl font-semibold tabular-nums text-red-800">{{ $import->rejected_rows }}</div>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-3">Lignes en erreur ou en doublon</h2>
    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-4 py-2">Ligne</th><th class="px-4 py-2">Statut</th><th class="px-4 py-2">Raison</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($import->rows as $row)
                    <tr>
                        <td class="px-4 py-2 tabular-nums">{{ $row->row_number }}</td>
                        <td class="px-4 py-2">{{ $row->status }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $row->error_message }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">Aucune ligne en erreur — import parfaitement propre.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
