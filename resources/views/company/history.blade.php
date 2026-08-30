@extends('layouts.app')

@section('title', 'Historique des vérifications')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Historique des vérifications</h1>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Université</th>
                    <th class="px-4 py-3">Candidat</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $log->type }}</td>
                        <td class="px-4 py-3">{{ $log->university?->name }}</td>
                        <td class="px-4 py-3">{{ $log->student?->fullName() ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($log->verificationDocument)
                                <a href="{{ route('company.verification.download', $log->verificationDocument) }}" class="hover:underline">Télécharger</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Aucune vérification enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
