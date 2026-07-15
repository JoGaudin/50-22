<?php

namespace App\UseCases\Role;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;

class SyncRoleRightsUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {}

    /**
     * @param array<int> $rightIds
     */
    public function execute(Role $role, array $rightIds): void
    {
        $this->roles->syncRights($role, $rightIds);
    }
}
