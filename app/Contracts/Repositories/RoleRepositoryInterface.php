<?php

namespace App\Contracts\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function findById(string $id): ?Role;

    public function findBySlug(string $slug): ?Role;

    /**
     * @return Collection<int, Role>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Role;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Role $role, array $data): Role;

    public function delete(Role $role): bool;

    /**
     * @param array<string> $rightIds
     */
    public function syncRights(Role $role, array $rightIds): void;
}
