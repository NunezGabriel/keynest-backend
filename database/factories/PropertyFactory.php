<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::where('user_type', 'landlord')->inRandomOrder()->first()->id,
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(3),
            'property_type' => fake()->randomElement(['casa', 'departamento']),
            'price' => fake()->numberBetween(1000, 5000),
            'maintenance_cost' => fake()->optional()->numberBetween(50, 300),
            'is_rent' => fake()->boolean(),
            'square_meters' => fake()->numberBetween(50, 200),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 3),
            'pets_allowed' => fake()->boolean(),
            'location' => fake()->address(),
            'status' => 'disponible',
        ];
    }
}
