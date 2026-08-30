@extends('layouts.app')

@section('title', 'Importer un palmarès')

@section('content')
    <h1 class="text-2xl font-semibold mb-2">Importer un palmarès</h1>
    <p class="text-sm text-slate-500 mb-6">
        Étape 1 sur 2 — choisissez la faculté et le fichier (CSV ou Excel). Une prévisualisation
        vous sera montrée avant toute écriture en base de données.
    </p>

    <form method="POST" action="{{ route('university.palmares.preview') }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Faculté</label>
            <select name="faculty_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                <option value="">Sélectionner…</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fichier (CSV, XLS, XLSX — 5 Mo max)</label>
            <input type="file" name="file" accept=".csv,.txt,.xls,.xlsx" class="w-full text-sm" required>
            <p class="text-xs text-slate-400 mt-1">Colonnes attendues : matricule, nom, postnom, prenom, sexe, date_naissance, mention, moyenne.</p>
        </div>
        <button class="bg-slate-900 text-white text-sm px-5 py-2.5 rounded-md">Prévisualiser</button>
    </form>
@endsection
