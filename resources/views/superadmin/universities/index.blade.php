@extends('layouts.app')

@section('title', 'Universités')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Universités</h1>

    <form method="POST" action="{{ route('superadmin.universities.store') }}" class="bg-white border border-slate-200 rounded-lg p-5 mb-8 flex flex-wrap gap-3 items-end">
        @csrf
        <div>
            <label class="block text-xs font-medium mb-1">Nom</label>
            <input type="text" name="name" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-64" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Sigle</label>
            <input type="text" name="short_name" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-28" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Ville</label>
            <input type="text" name="city" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-40">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Pays</label>
            <input type="text" name="country" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-40">
        </div>
        <button class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md">Créer</button>
    </form>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-4 py-3">Nom</th><th class="px-4 py-3">Étudiants</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($universities as $university)
                    <tr>
                        <td class="px-4 py-3">{{ $university->name }} <span class="text-slate-400 font-mono text-xs">({{ $university->short_name }})</span></td>
                        <td class="px-4 py-3 tabular-nums">{{ $university->students_count }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full {{ $university->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">{{ $university->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($university->status === 'active')
                                <form method="POST" action="{{ route('superadmin.universities.suspend', $university) }}" class="inline">
                                    @csrf
                                    <button class="text-xs text-red-700 hover:underline">Suspendre</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('superadmin.universities.reactivate', $university) }}" class="inline">
                                    @csrf
                                    <button class="text-xs text-emerald-700 hover:underline">Réactiver</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Aucune université.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $universities->links() }}</div>
@endsection
