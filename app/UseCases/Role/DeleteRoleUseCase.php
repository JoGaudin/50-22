<?php

namespace App\UseCases\Role;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use RuntimeException;

class DeleteRoleUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {}

    /**
     * @throws RuntimeException if the role is protected
     */
    public function execute(Role $role): void
    {
        if (in_array($role->slug, ['admin', 'user'], true)) {
            throw new RuntimeException(__('Ce rôle est protégé et ne peut pas être supprimé.'));
        }

        $this->roles->delete($role);
    }
}
