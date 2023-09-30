<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SoilRequirement>
 */
class SoilRequirementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crop' => 'Rice',
            'water' => 'Needs lots of water',
            'location' => 'Kaduna',
            'temperature' => '40˚ Censius',
            'details' => $this->faker->sentences(rand(3, 2), true),
        ];
    }
}
