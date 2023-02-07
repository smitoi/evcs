<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Station;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var User $administrator */
        $administrator = User::query()->create([
            'name' => 'Administrator',
            'email' => 'admin@virta.global',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make(
                UserFactory::DEFAULT_PASSWORD
            ),
        ]);

        $administrator->assignRole(
            Role::findOrCreate(RoleEnum::ADMIN->value)
        );

        /** @var User $customer */
        $customer = User::query()->create([
            'name' => 'Customer',
            'email' => 'customer@virta.global',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make(
                UserFactory::DEFAULT_PASSWORD
            ),
        ]);

        $customer->assignRole(
            Role::findOrCreate(RoleEnum::CUSTOMER->value)
        );

        // We create them in "batches" to properly generate the tree-like structure
        for ($index = 1; $index < 5; $index++) {
            Company::factory(5)->create();
        }

        Station::factory(100)->create();
    }
}
