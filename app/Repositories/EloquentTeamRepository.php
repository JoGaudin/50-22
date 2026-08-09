<?php

namespace App\Repositories;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class EloquentTeamRepository implements TeamRepositoryInterface
{
    public function findById(string $id): ?Team
    {
        return Team::find($id);
    }

    public function all(): Collection
    {
        return Team::query()->orderBy('name')->get();
    }

    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(Team $team, array $data): Team
    {
        $team->update($data);

        return $team->fresh() ?? $team;
    }

    public function delete(Team $team): bool
    {
        return (bool) $team->delete();
    }
}
