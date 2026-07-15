<?php

namespace App\Contracts\Repositories;

use App\Models\Right;
use Illuminate\Database\Eloquent\Collection;

interface RightRepositoryInterface
{
    public function findById(string $id): ?Right;

    public function findBySlug(string $slug): ?Right;

    /**
     * @return Collection<int, Right>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Right;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Right $right, array $data): Right;

    public function delete(Right $right): bool;
}
