<?php

namespace Database\Factories;

use App\Models\Station;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Station>
 */
class StationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'latitude' => $this->faker->randomFloat(min: -90, max: 90),
            'longitude' => $this->faker->randomFloat(min: -180, max: 180),
            'company_id' => $this->faker->randomElement(Company::all())->id,
        ];
    }
}
