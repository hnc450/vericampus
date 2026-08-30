@extends('layouts.app')

@section('title', 'Aperçu de l\'import')

@section('content')
    <h1 class="text-2xl font-semibold mb-2">Aperçu de l'import — {{ $faculty->name }}</h1>
    <p class="text-sm text-slate-500 mb-6">Étape 2 sur 2 — vérifiez le rapport, puis confirmez pour lancer l'import définitif.</p>

    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <div class="text-xs uppercase text-slate-500">Lignes lues</div>
            <div class="text-xl font-semibold tabular-nums">{{ $preview['total'] }}</div>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
            <div class="text-xs uppercase text-emerald-700">Seront importées</div>
            <div class="text-xl font-semibold tabular-nums text-emerald-800">{{ $preview['would_import'] }}</div>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="text-xs uppercase text-amber-700">Doublons</div>
            <div class="text-xl font-semibold tabular-nums text-amber-800">{{ $preview['duplicates'] }}</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-xs uppercase text-red-700">Rejetées</div>
            <div class="text-xl font-semibold tabular-nums text-red-800">{{ $preview['would_reject'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto mb-8">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-4 py-2">Ligne</th><th class="px-4 py-2">Matricule</th><th class="px-4 py-2">Nom</th><th class="px-4 py-2">Statut</th><th class="px-4 py-2">Détail</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach(array_slice($preview['rows'], 0, 100) as $row)
                    <tr>
                        <td class="px-4 py-2 tabular-nums">{{ $row['row_number'] }}</td>
                        <td class="px-4 py-2 font-mono text-xs">{{ $row['data']['matricule'] ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $row['data']['nom'] ?? '—' }} {{ $row['data']['prenom'] ?? '' }}</td>
                        <td class="px-4 py-2">
                            <span @class([
                                'text-xs px-2 py-1 rounded-full',
                                'bg-emerald-100 text-emerald-800' => $row['status'] === 'imported',
                                'bg-amber-100 text-amber-800' => $row['status'] === 'duplicate',
                                'bg-red-100 text-red-800' => $row['status'] === 'rejected',
                            ])>{{ $row['status'] }}</span>
                        </td>
                        <td class="px-4 py-2 text-slate-500">{{ $row['error'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($preview['total'] > 100)
            <div class="px-4 py-2 text-xs text-slate-400">… et {{ $preview['total'] - 100 }} lignes supplémentaires (aperçu limité aux 100 premières).</div>
        @endif
    </div>

    <form method="POST" action="{{ route('university.palmares.store') }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-4">
        @csrf
        <input type="hidden" name="faculty_id" value="{{ $faculty->id }}">
        <p class="text-xs text-slate-500">Sélectionnez à nouveau le même fichier pour confirmer l'import.</p>

        <div>
            <label class="block text-sm font-medium mb-1">Année académique</label>
            <select name="academic_year_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                @foreach($faculty->university->academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Promotion</label>
            <select name="promotion_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                @foreach($faculty->promotions as $promotion)
                    <option value="{{ $promotion->id }}">{{ $promotion->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fichier</label>
            <input type="file" name="file" accept=".csv,.txt,.xls,.xlsx" class="w-full text-sm" required>
        </div>

        <button class="bg-slate-900 text-white text-sm px-5 py-2.5 rounded-md">Confirmer l'import</button>
        <a href="{{ route('university.palmares.create') }}" class="text-sm text-slate-500 ml-3">Annuler</a>
    </form>
@endsection
