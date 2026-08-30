<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Faculty;
use App\Models\Student;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLog) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Student::class);

        $students = Student::with(['faculty', 'department'])
            ->when($request->filled('q'), fn ($q) => $q->search($request->string('q')))
            ->when($request->filled('faculty_id'), fn ($q) => $q->where('faculty_id', $request->integer('faculty_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        $faculties = Faculty::orderBy('name')->get();

        return view('university.students.index', compact('students', 'faculties'));
    }

    public function create(): View
    {
        $this->authorize('create', Student::class);

        $faculties = Faculty::with('departments')->orderBy('name')->get();

        return view('university.students.create', compact('faculties'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $university = $request->user()->university;

        $data = $request->safe()->except('photo');
        $data['unique_student_id'] = Student::generateUniqueStudentId($university);
        $data['status'] = 'active';

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('student-photos/'.$university->id, 'public');
        }

        $student = Student::create($data);

        $this->auditLog->record('student.created', $student, [], ['unique_student_id' => $student->unique_student_id]);

        return redirect()->route('university.students.show', $student)
            ->with('status', "Étudiant créé — ID : {$student->unique_student_id}.");
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $student->load(['faculty', 'department', 'academicRecords.academicYear', 'academicRecords.promotion', 'academicRecords.degree']);

        return view('university.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $this->authorize('update', $student);

        $faculties = Faculty::with('departments')->orderBy('name')->get();

        return view('university.students.edit', compact('student', 'faculties'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $university = $request->user()->university;
        $old = $student->only(['faculty_id', 'department_id', 'student_number', 'last_name', 'first_name', 'status']);

        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('student-photos/'.$university->id, 'public');
        }

        $student->update($data);

        $this->auditLog->record('student.updated', $student, $old, $data);

        return redirect()->route('university.students.show', $student)->with('status', 'Fiche étudiant mise à jour.');
    }

    public function deactivate(Student $student): RedirectResponse
    {
        $this->authorize('deactivate', $student);

        $student->update(['status' => 'suspended']);

        $this->auditLog->record('student.deactivated', $student);

        return back()->with('status', 'Étudiant désactivé.');
    }
}
