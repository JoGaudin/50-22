<?php

namespace App\Contracts\Repositories;

use App\Models\GameMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface GameMatchRepositoryInterface
{
    public function findById(string $id): ?GameMatch;

    /**
     * @return Collection<int, GameMatch>
     */
    public function all(): Collection;

    /**
     * @return Collection<int, GameMatch>
     */
    public function forReferee(User $user): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): GameMatch;

    /**
     * @param array<string, mixed> $data
     */
    public function update(GameMatch $match, array $data): GameMatch;

    public function delete(GameMatch $match): bool;
}
