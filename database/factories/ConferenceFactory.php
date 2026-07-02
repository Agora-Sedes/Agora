<?php

namespace Database\Factories;

use App\Models\Conference;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conference>
 */
class ConferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ends_at = fake()->dateTime('+1 year');
        return [
            'title' => fake()->realText(50),
            'description' => fake()->realText(100),
            'starts_at' => fake()->dateTime($ends_at),
            'ends_at' => $ends_at,
            'price' => 12000.00,
        ];
    }
}
