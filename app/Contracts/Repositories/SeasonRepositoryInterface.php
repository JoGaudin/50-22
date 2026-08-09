<?php

namespace App\Contracts\Repositories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

interface SeasonRepositoryInterface
{
    public function findById(string $id): ?Season;

    /**
     * @return Collection<int, Season>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Season;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Season $season, array $data): Season;

    public function delete(Season $season): bool;
}
