@extends('layouts.app')

@section('title', 'Comptes administrateurs')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Comptes administrateurs</h1>

    <form method="POST" action="{{ route('superadmin.admin-users.store') }}" class="bg-white border border-slate-200 rounded-lg p-5 mb-8 flex flex-wrap gap-3 items-end" x-data="{ role: 'university_admin' }">
        @csrf
        <div>
            <label class="block text-xs font-medium mb-1">Nom</label>
            <input type="text" name="name" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-56" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Email</label>
            <input type="email" name="email" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-64" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Rôle</label>
            <select name="role" x-model="role" class="border border-slate-300 rounded-md px-3 py-2 text-sm">
                <option value="university_admin">Administrateur université</option>
                <option value="company_user">Compte entreprise</option>
            </select>
        </div>
        <div x-show="role === 'university_admin'">
            <label class="block text-xs font-medium mb-1">Université</label>
            <select name="university_id" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-56">
                @foreach($universities as $university)
                    <option value="{{ $university->id }}">{{ $university->name }}</option>
                @endforeach
            </select>
        </div>
        <div x-show="role === 'company_user'">
            <label class="block text-xs font-medium mb-1">Entreprise</label>
            <select name="company_id" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-56">
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md">Créer le compte</button>
    </form>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-4 py-3">Nom</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Rattachement</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->university?->name ?? $user->company?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">{{ $user->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($user->status === 'active')
                                <form method="POST" action="{{ route('superadmin.admin-users.suspend', $user) }}">
                                    @csrf
                                    <button class="text-xs text-red-700 hover:underline">Suspendre</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Aucun compte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
