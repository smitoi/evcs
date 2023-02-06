<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    /**
     * @throws ReflectionException
     */
    public static function getPermissionForModel(string $model, PermissionEnum $permission): string
    {
        if (Str::contains($model, '\\')) {
            $model = Str::snake(
                (new ReflectionClass($model))->getShortName()
            );
        }

        return "{$model}_$permission->value";
    }

    /**
     * @throws ReflectionException
     */
    private static function grantModelPermission(Role $role, string $model, PermissionEnum $permission): void
    {
        $role->givePermissionTo(
            Permission::findOrCreate(
                self::getPermissionForModel($model, $permission)
            )
        );
    }

    /**
     * @throws ReflectionException
     */
    public static function grantPermissionsForModel(Role $role, string $model, PermissionEnum...$permissions): void
    {
        foreach ($permissions as $permission) {
            self::grantModelPermission($role, $model, $permission);
        }
    }
}
