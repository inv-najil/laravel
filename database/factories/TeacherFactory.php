<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'teacher']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'emp_id' => $this->faker->unique()->numerify('EMP###'),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->numerify('##########'),
            'subject_specialization' => $this->faker->word(),
            'date_of_joining' => $this->faker->date(),
            'status' => 'active'
        ];
    }
}
