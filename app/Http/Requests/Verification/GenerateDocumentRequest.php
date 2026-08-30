<?php

namespace App\Http\Requests\Verification;

use Illuminate\Foundation\Http\FormRequest;

class GenerateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isCompanyUser() || $this->user()->isUniversityAdmin() || $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'string', 'exists:students,id'],
            'academic_record_id' => ['required', 'integer', 'exists:student_academic_records,id'],
        ];
    }
}
