<?php

namespace App\Http\Requests\Palmares;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportPalmaresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isUniversityAdmin() || $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $universityId = $this->user()->university_id;

        return [
            'faculty_id' => [
                'required',
                Rule::exists('faculties', 'id')->where('university_id', $universityId),
            ],
            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')->where('university_id', $universityId),
            ],
            'promotion_id' => [
                'required',
                Rule::exists('promotions', 'id')->where('faculty_id', $this->input('faculty_id')),
            ],
            // Le fichier lui-même : extensions et taille strictement limitées
            // (cahier des charges §16 — validation des fichiers uploadés).
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
        ];
    }
}
