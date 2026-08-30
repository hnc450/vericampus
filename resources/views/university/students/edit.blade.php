@extends('layouts.app')

@section('title', 'Modifier — '.$student->fullName())

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Modifier {{ $student->fullName() }}</h1>

    <form method="POST" action="{{ route('university.students.update', $student) }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-2xl space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Faculté</label>
                <select name="faculty_id" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}" @selected(old('faculty_id', $student->faculty_id) == $faculty->id)>{{ $faculty->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Matricule</label>
                <input type="text" name="student_number" value="{{ old('student_number', $student->student_number) }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nom</label>
                <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Postnom</label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Prénom</label>
                <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Statut</label>
                <select name="status" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm" required>
                    @foreach(['active' => 'Actif', 'graduated' => 'Diplômé', 'dropped' => 'Abandon', 'suspended' => 'Suspendu'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $student->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Date de naissance</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', optional($student->birth_date)->toDateString()) }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm">
            </div>
        </div>

        <div class="pt-2">
            <button class="bg-slate-900 text-white text-sm px-5 py-2.5 rounded-md">Enregistrer</button>
        </div>
    </form>
@endsection
