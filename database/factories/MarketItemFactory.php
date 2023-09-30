<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketItem>
 */
class MarketItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'Rice' => ['Cos Rice', 'Red Maize', 'Ofada'][rand(0, 2)],
            'Maize' => ['Red Maize', 'White Maize', 'Gold Maize'][rand(0, 2)]
        ];

        $name = ['Rice', 'Maize'][rand(0, 1)];

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'slug' => $this->faker->slug,
            'image' => $this->faker->imageUrl,
            'name' => $name,
            'type' => $types[$name],
            'grade' => ['A', 'B', 'C', 'D', 'E'][rand(0, 4)],
            'quantity' => rand(10, 200),
            'location' => "{$this->faker->latitude},{$this->faker->longitude}",
            'address' => $this->faker->address,
            'country' => 'Nigeria',
            'state' => ["Kaduna", "Benue", "Kano"][rand(0, 2)],
            'city' => $this->faker->city,
            'active' => $this->faker->boolean,
            'approved' => $this->faker->boolean,
        ];
    }
}
