<?php

namespace App\UseCases\Role;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;

class CreateRoleUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {}

    public function execute(string $name, string $slug, ?string $description = null): Role
    {
        return $this->roles->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);
    }
}
