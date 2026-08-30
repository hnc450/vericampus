@extends('layouts.app')

@section('title', 'Autorité signataire')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Autorité signataire</h1>

    <div class="bg-white border border-slate-200 rounded-lg p-5 mb-8">
        <div class="text-xs uppercase text-slate-500 mb-1">Signature active</div>
        @if($active)
            <div class="text-lg font-medium">{{ $active->authority_name }}</div>
            <div class="text-sm text-slate-500">{{ $active->authority_title }} — valide depuis le {{ $active->valid_from->format('d/m/Y') }}</div>
        @else
            <div class="text-sm text-amber-700">Aucune signature active. Les documents générés n'afficheront pas de signature tant qu'aucune n'est enregistrée.</div>
        @endif
    </div>

    <h2 class="text-lg font-semibold mb-3">Enregistrer une nouvelle autorité</h2>
    <p class="text-xs text-slate-500 mb-4">
        L'enregistrement d'une nouvelle signature révoque automatiquement l'ancienne pour les futurs documents —
        les documents déjà générés conservent la signature qui était active au moment de leur génération.
    </p>

    <form method="POST" action="{{ route('university.signature.store') }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input type="text" name="authority_name" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fonction</label>
            <input type="text" name="authority_title" placeholder="Recteur, Doyen…" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Valide à partir du</label>
                <input type="date" name="valid_from" value="{{ now()->toDateString() }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Valide jusqu'au (optionnel)</label>
                <input type="date" name="valid_until" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Image de signature (PNG/JPG, 1 Mo max)</label>
            <input type="file" name="signature_image" accept="image/png,image/jpeg" class="w-full text-sm" required>
        </div>
        <button class="bg-slate-900 text-white text-sm px-5 py-2.5 rounded-md">Enregistrer</button>
    </form>

    <h2 class="text-lg font-semibold mt-10 mb-3">Historique</h2>
    <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
        @forelse($history as $signature)
            <div class="px-4 py-3 text-sm flex justify-between items-center">
                <span>{{ $signature->authority_name }} — {{ $signature->authority_title }}</span>
                <span class="text-xs px-2 py-1 rounded-full {{ $signature->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $signature->status }}</span>
            </div>
        @empty
            <div class="px-4 py-6 text-sm text-slate-400 text-center">Aucun historique.</div>
        @endforelse
    </div>
@endsection
