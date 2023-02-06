<?php

use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Role::create([
            'name' => RoleEnum::ADMIN
        ]);

        Role::create([
            'name' => RoleEnum::CUSTOMER,
        ]);
    }
};
