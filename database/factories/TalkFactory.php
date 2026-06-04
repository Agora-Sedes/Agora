<?php

namespace Database\Factories;

use App\Models\Talk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Talk>
 */
class TalkFactory extends Factory
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
            'title' => fake()->text(50),
            'description' => fake()->text(100),
            'starts_at' => fake()->dateTime($ends_at),
            'ends_at' => $ends_at,
            'speaker' => fake()->name(),
            'speaker_background' => fake()->realText(100),
        ];
    }
}
