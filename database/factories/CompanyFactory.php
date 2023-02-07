<?php

namespace Database\Factories;

use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parent = $this->faker->randomElement(Company::all());

        return [
            'name' => $this->faker->company,
            'parent_id' => $parent?->id,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Company $company) {
            if ($company->parent_id) {
                CompanyService::addParentHierarchy($company);
            }
        });
    }
}
