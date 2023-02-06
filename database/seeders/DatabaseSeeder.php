<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
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
        /** @var User $user */
        $user = User::query()->create([
            'name' => 'Administrator',
            'email' => 'admin@mitstefan.dev',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('password'),
        ]);

        $user->assignRole(
            Role::findOrCreate(RoleEnum::ADMIN->value)
        );
    }
}
