<?php

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Station;
use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('stations', static function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->string('address');
            $table->double('latitude');
            $table->double('longitude');
            $table->foreignIdFor(Company::class)->references('id')->on('companies')->cascadeOnDelete();
            $table->timestamps();
        });

        PermissionService::grantPermissionsForModel(
            Role::findByName(
                RoleEnum::ADMIN->value,
            ),
            Station::class,
            ...PermissionEnum::cases(),
        );

        PermissionService::grantPermissionsForModel(
            Role::findByName(
                RoleEnum::CUSTOMER->value,
            ),
            Station::class,
            PermissionEnum::PERMISSION_LIST,
            PermissionEnum::PERMISSION_SHOW,
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
