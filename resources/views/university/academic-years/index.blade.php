@extends('layouts.app')

@section('title', 'Années académiques')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Années académiques</h1>

    <form method="POST" action="{{ route('university.academic-years.store') }}" class="bg-white border border-slate-200 rounded-lg p-5 mb-8 flex flex-wrap gap-3 items-end">
        @csrf
        <div>
            <label class="block text-xs font-medium mb-1">Libellé (ex. 2024-2025)</label>
            <input type="text" name="label" value="{{ old('label') }}" class="border border-slate-300 rounded-md px-3 py-2 text-sm w-40" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Début</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" class="border border-slate-300 rounded-md px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Fin</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" class="border border-slate-300 rounded-md px-3 py-2 text-sm" required>
        </div>
        <button class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md">Créer</button>
    </form>

    <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
        @forelse($years as $year)
            <div class="px-4 py-3 text-sm flex justify-between items-center">
                <span>{{ $year->label }} <span class="text-slate-400">({{ $year->start_date->format('d/m/Y') }} — {{ $year->end_date->format('d/m/Y') }})</span></span>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $year->status }}</span>
                    @if($year->status === 'active')
                        <form method="POST" action="{{ route('university.academic-years.close', $year) }}" onsubmit="return confirm('Clôturer cette année académique ?');">
                            @csrf
                            <button class="text-xs text-red-700 hover:underline">Clôturer</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-4 py-6 text-sm text-slate-400 text-center">Aucune année académique.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $years->links() }}</div>
@endsection
