<?php

namespace App\Contracts\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

interface TeamRepositoryInterface
{
    public function findById(string $id): ?Team;

    /**
     * @return Collection<int, Team>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Team;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Team $team, array $data): Team;

    public function delete(Team $team): bool;
}
