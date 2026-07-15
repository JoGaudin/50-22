<?php

namespace App\UseCases\Role;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Right;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class ListRolesUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {}

    /**
     * @return Collection<int, Role>
     */
    public function execute(): Collection
    {
        return $this->roles->all();
    }
}
