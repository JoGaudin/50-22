<?php

namespace App\Contracts\Repositories;

use App\Models\Journee;
use Illuminate\Database\Eloquent\Collection;

interface JourneeRepositoryInterface
{
    public function findById(string $id): ?Journee;

    /**
     * @return Collection<int, Journee>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Journee;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Journee $journee, array $data): Journee;

    public function delete(Journee $journee): bool;
}
