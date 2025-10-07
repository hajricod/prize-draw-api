<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw>
 */
class DrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Draw ' . $this->faker->word(),
            'description' => $this->faker->sentence(),
            'draw_date' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
        ];
    }
}
