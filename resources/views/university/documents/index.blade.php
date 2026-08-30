@extends('layouts.app')

@section('title', 'Documents de vérification')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Documents de vérification</h1>

    <div class="bg-white rounded-lg border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Étudiant</th>
                    <th class="px-4 py-3">Généré par</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($documents as $document)
                    <tr>
                        <td class="px-4 py-3">{{ $document->student->fullName() }}</td>
                        <td class="px-4 py-3">{{ $document->generatedByCompany?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $document->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'text-xs px-2 py-1 rounded-full',
                                'bg-emerald-100 text-emerald-800' => $document->currentStatus() === 'verified',
                                'bg-red-100 text-red-800' => $document->currentStatus() === 'revoked',
                                'bg-slate-100 text-slate-600' => $document->currentStatus() === 'expired',
                            ])>{{ $document->currentStatus() }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($document->currentStatus() === 'verified')
                                <form method="POST" action="{{ route('university.documents.revoke', $document) }}" onsubmit="return confirm('Révoquer ce document ?');" class="inline-flex gap-2">
                                    @csrf
                                    <input type="text" name="revoked_reason" placeholder="Motif" required class="border border-slate-300 rounded-md px-2 py-1 text-xs">
                                    <button class="text-xs text-red-700 hover:underline">Révoquer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Aucun document généré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $documents->links() }}</div>
@endsection
