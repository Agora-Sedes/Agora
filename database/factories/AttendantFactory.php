<?php

namespace Database\Factories;

use App\Models\Attendant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendant>
 */
class AttendantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_draft' => fake()->boolean(),
            'was_present' => fake()->boolean(),
            'government_id' => fake()->creditCardNumber(),
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
        ];
    }
}
