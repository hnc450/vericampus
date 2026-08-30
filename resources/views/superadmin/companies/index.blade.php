@extends('layouts.app')

@section('title', 'Entreprises')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Entreprises</h1>

    <form method="POST" action="{{ route('superadmin.companies.store') }}" class="bg-white border border-slate-200 rounded-lg p-5 mb-8 flex flex-wrap gap-3 items-end">
        @csrf
        <div>
            <label class="block text-xs font-medium mb-1">Nom</label>
            <input type="text" name="name" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-64" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Secteur</label>
            <input type="text" name="sector" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-48">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Email</label>
            <input type="email" name="email" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-64">
        </div>
        <button class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md">Créer</button>
    </form>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-4 py-3">Nom</th><th class="px-4 py-3">Secteur</th><th class="px-4 py-3">Comptes</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($companies as $company)
                    <tr>
                        <td class="px-4 py-3">{{ $company->name }}</td>
                        <td class="px-4 py-3">{{ $company->sector }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $company->users_count }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full {{ $company->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">{{ $company->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($company->status === 'active')
                                <form method="POST" action="{{ route('superadmin.companies.suspend', $company) }}">
                                    @csrf
                                    <button class="text-xs text-red-700 hover:underline">Suspendre</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Aucune entreprise.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $companies->links() }}</div>
@endsection
