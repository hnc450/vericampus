<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\Student;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'faculty_id' => Faculty::factory(),
            'student_number' => fake()->unique()->numerify('MAT-#######'),
            'unique_student_id' => 'UNI-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'status' => 'active',
        ];
    }
}
