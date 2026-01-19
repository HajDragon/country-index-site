<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = strtoupper(fake()->unique()->lexify('???'));

        return [
            'Code' => $code,
            'Name' => fake()->country(),
            'Continent' => fake()->randomElement(['Asia', 'Europe', 'Africa', 'North America', 'South America', 'Oceania', 'Antarctica']),
            'Region' => fake()->word(),
            'SurfaceArea' => fake()->randomFloat(2, 1000, 10000000),
            'IndepYear' => fake()->optional()->numberBetween(1000, 2024),
            'Population' => fake()->numberBetween(100000, 1500000000),
            'LifeExpectancy' => fake()->optional()->randomFloat(1, 40, 85),
            'GNP' => fake()->optional()->randomFloat(2, 1000, 10000000),
            'GNPOld' => fake()->optional()->randomFloat(2, 1000, 10000000),
            'LocalName' => fake()->word(),
            'GovernmentForm' => fake()->word(),
            'HeadOfState' => fake()->optional()->name(),
            'Capital' => null,
            'Code2' => strtoupper(fake()->lexify('??')),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }
}
