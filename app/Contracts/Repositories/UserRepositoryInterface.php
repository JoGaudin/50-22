<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    /**
     * @param array{
     *     search?: string,
     *     sort_by?: string,
     *     sort_dir?: string,
     *     limit?: int,
     *     page?: int,
     * } $filters
     * @return LengthAwarePaginator<User>
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): User;

    /**
     * @param array<string, mixed> $data
     */
    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    /**
     * @param array<string> $roleIds
     */
    public function syncRoles(User $user, array $roleIds): void;
}
