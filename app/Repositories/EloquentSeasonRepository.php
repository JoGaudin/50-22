<?php

namespace App\Repositories;

use App\Contracts\Repositories\SeasonRepositoryInterface;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

class EloquentSeasonRepository implements SeasonRepositoryInterface
{
    public function findById(string $id): ?Season
    {
        return Season::find($id);
    }

    public function all(): Collection
    {
        return Season::query()->with('league:id,name')->orderBy('start', 'desc')->get();
    }

    public function create(array $data): Season
    {
        return Season::create($data);
    }

    public function update(Season $season, array $data): Season
    {
        $season->update($data);

        return $season->fresh() ?? $season;
    }

    public function delete(Season $season): bool
    {
        return (bool) $season->delete();
    }
}
