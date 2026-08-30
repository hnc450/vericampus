<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Student::class);
    }

    public function rules(): array
    {
        $universityId = $this->user()->university_id;

        return [
            'faculty_id' => [
                'required',
                Rule::exists('faculties', 'id')->where('university_id', $universityId),
            ],
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where('faculty_id', $this->input('faculty_id')),
            ],
            'student_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students')->where('university_id', $universityId),
            ],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'sex' => ['nullable', Rule::in(['M', 'F'])],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
