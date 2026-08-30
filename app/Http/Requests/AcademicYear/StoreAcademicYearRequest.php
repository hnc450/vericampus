<?php

namespace App\Http\Requests\AcademicYear;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isUniversityAdmin() || $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $universityId = $this->user()->university_id;

        return [
            'label' => [
                'required',
                'string',
                'max:20',
                Rule::unique('academic_years')->where('university_id', $universityId),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }
}
