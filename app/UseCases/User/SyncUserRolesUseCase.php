<?php

namespace App\UseCases\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class SyncUserRolesUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    /**
     * @param array<int> $roleIds
     */
    public function execute(User $user, array $roleIds): void
    {
        $this->users->syncRoles($user, $roleIds);
    }
}
