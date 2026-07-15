<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function findById(string $id): ?Role
    {
        return Role::find($id);
    }

    public function findBySlug(string $slug): ?Role
    {
        return Role::query()->where('slug', $slug)->first();
    }

    public function all(): Collection
    {
        return Role::query()
            ->with('rights:id,name,slug')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role->fresh() ?? $role;
    }

    public function delete(Role $role): bool
    {
        return (bool) $role->delete();
    }

    public function syncRights(Role $role, array $rightIds): void
    {
        $role->rights()->sync($rightIds);
    }
}
