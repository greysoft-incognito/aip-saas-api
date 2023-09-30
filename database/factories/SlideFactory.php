<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slide>
 */
class SlideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug,
            'image' => $this->faker->imageUrl,
            'title' => str($this->faker->words(rand(5, 7), true))->title(),
            'line1' => str($this->faker->words(rand(5, 7), true))->title(),
            'line2' => str($this->faker->words(rand(5, 7), true))->title(),
            'line3' => str($this->faker->words(rand(5, 7), true))->title(),
            'active' => $this->faker->boolean,
        ];
    }
}
