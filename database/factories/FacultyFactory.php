<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'name' => fake()->unique()->words(3, true),
            'code' => mb_strtoupper(fake()->unique()->lexify('???')),
            'status' => 'active',
        ];
    }
}
