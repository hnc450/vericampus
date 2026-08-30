<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Faculty::class);
    }

    public function rules(): array
    {
        $universityId = $this->user()->university_id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('faculties')->where('university_id', $universityId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
