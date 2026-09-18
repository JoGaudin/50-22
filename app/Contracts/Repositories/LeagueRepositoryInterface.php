<?php

namespace App\Contracts\Repositories;

use App\Models\League;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface LeagueRepositoryInterface
{
    public function findById(string $id): ?League;

    /**
     * @return Collection<int, League>
     */
    public function all(): Collection;

    /**
     * @return Collection<int, League>
     */
    public function forUser(User $user): Collection;

    /**
     * @return Collection<int, League>
     */
    public function availableForUser(User $user): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): League;

    /**
     * @param array<string, mixed> $data
     */
    public function update(League $league, array $data): League;

    public function delete(League $league): bool;
}
