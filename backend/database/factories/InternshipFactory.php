<?php

namespace Database\Factories;

use App\Models\Internship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Internship>
 */
class InternshipFactory extends Factory
{
    protected $model = Internship::class;

    public function definition(): array
    {
        return [
            'student_name' => fake()->name(),
            'company_name' => fake()->company(),
            'position' => fake()->randomElement([
                'Backend Developer Intern',
                'Frontend Engineer Intern',
                'DevOps Engineer Intern',
                'Data Analyst Intern',
                'UI/UX Designer Intern',
            ]),
            'start_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'status' => fake()->randomElement(['Pending', 'Diterima', 'Selesai']),
        ];
    }
}
