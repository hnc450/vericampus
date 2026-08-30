<?php

namespace Database\Factories;

use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class UniversityFactory extends Factory
{
    protected $model = University::class;

    public function definition(): array
    {
        return [
            'name' => 'Université '.fake()->city(),
            'short_name' => mb_strtoupper(fake()->lexify('???')),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'status' => 'active',
        ];
    }
}
