<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' =>  $this->faker->slug,
            'image' => $this->faker->imageUrl,
            'title' => str($this->faker->words(rand(3, 5), true))->title(),
            'content' => $this->faker->sentences(3, 2),
            'active' =>  $this->faker->boolean()
        ];
    }
}
