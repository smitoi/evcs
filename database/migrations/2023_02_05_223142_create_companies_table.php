<?php

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Company;
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
        Schema::create('companies', static function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->foreignIdFor(Company::class, 'parent_id')->nullable()->references('id')->on('companies')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('companies_hierarchy_levels', static function (Blueprint $table) {
            $table->foreignIdFor(Company::class, 'ancestor_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignIdFor(Company::class, 'descendant_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->integer('distance');
        });

        PermissionService::grantPermissionsForModel(
            Role::findByName(
                RoleEnum::ADMIN->value,
            ),
            Company::class,
            ...PermissionEnum::cases(),
        );

        PermissionService::grantPermissionsForModel(
            Role::findByName(
                RoleEnum::CUSTOMER->value,
            ),
            Company::class,
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
        Schema::dropIfExists('companies');
        Schema::dropIfExists('companies_hierarchy');
    }
};
