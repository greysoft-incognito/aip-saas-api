<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiseaseOutbreak>
 */
class DiseaseOutbreakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => $this->faker->imageUrl,
            'name' => str($this->faker->words(rand(3, 5), true))->title(),
            'description' => $this->faker->sentences(rand(3, 2), true),
            'reported_at' =>  $this->faker->dateTime
        ];
    }
}
