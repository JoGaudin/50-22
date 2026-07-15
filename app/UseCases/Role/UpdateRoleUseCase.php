<?php

namespace App\UseCases\Role;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use RuntimeException;

class UpdateRoleUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @throws RuntimeException if attempting to change the slug of a protected role
     */
    public function execute(Role $role, array $data): Role
    {
        if (in_array($role->slug, ['admin', 'user'], true) && isset($data['slug']) && $data['slug'] !== $role->slug) {
            throw new RuntimeException(__('Impossible de modifier le slug de ce rôle.'));
        }

        return $this->roles->update($role, $data);
    }
}
