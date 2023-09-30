<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => str($this->faker->words(rand(3, 5), true))->title(),
            'details' => $this->faker->sentences(rand(3, 2), true),
            'image' => $this->faker->imageUrl,
            'date' => $this->faker->dateTimeBetween('-30 days', '+30 days'),
            'color' => ["purple", 'red', 'green', 'blue', 'primary', 'primary', 'primary'][rand(0, 6)],
            'icon' => ["fas fa-handshake", null][rand(0, 1)],
            'days' => rand(1, 7),
        ];
    }
}
