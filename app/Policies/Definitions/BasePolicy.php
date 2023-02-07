<?php

namespace App\Policies\Definitions;

use App\Enums\PermissionEnum;
use App\Models\Company;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use ReflectionException;

abstract class BasePolicy
{
    use HandlesAuthorization;

    abstract protected function getClassName(): string;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     * @throws ReflectionException
     */
    public function viewAny(User $user): Response|bool
    {
        return $user->hasPermissionTo(
            PermissionService::getPermissionForModel(
                $this->getClassName(), PermissionEnum::PERMISSION_LIST,
            ),
        );
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Model $model
     * @return bool
     * @throws ReflectionException
     */
    public function view(User $user, Model $model): bool
    {
        return $user->hasPermissionTo(
            PermissionService::getPermissionForModel(
                $this->getClassName(), PermissionEnum::PERMISSION_SHOW,
            ),
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     * @throws ReflectionException
     */
    public function create(User $user): Response|bool
    {
        return $user->hasPermissionTo(
            PermissionService::getPermissionForModel(
                $this->getClassName(), PermissionEnum::PERMISSION_CREATE,
            ),
        );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Model $model
     * @return bool
     * @throws ReflectionException
     */
    public function update(User $user, Model $model): bool
    {
        return $user->hasPermissionTo(
            PermissionService::getPermissionForModel(
                $this->getClassName(), PermissionEnum::PERMISSION_UPDATE,
            ),
        );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Model $model
     * @return Response|bool
     * @throws ReflectionException
     */
    public function delete(User $user, Model $model): Response|bool
    {
        return $user->hasPermissionTo(
            PermissionService::getPermissionForModel(
                $this->getClassName(), PermissionEnum::PERMISSION_DELETE,
            ),
        );
    }
}
