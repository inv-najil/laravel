<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Teacher;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'student']),
            'teacher_id' => Teacher::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->numerify('##########'),
            'email' => $this->faker->safeEmail(),
            'roll_num' => $this->faker->unique()->numerify('ROLL###'),
            'class_grade' => $this->faker->word(),
            'dob' => $this->faker->date(),
            'admission_date' => $this->faker->date(),
            'status' => 'active'
        ];
    }
}
