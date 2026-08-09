<?php

namespace App\Repositories;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\GameMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentGameMatchRepository implements GameMatchRepositoryInterface
{
    public function findById(string $id): ?GameMatch
    {
        return GameMatch::find($id);
    }

    public function all(): Collection
    {
        return GameMatch::query()
            ->with(['journee:id,number', 'homeTeam:id,name', 'outsideTeam:id,name'])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function forReferee(User $user): Collection
    {
        return GameMatch::query()
            ->where('referee_id', $user->id)
            ->with(['journee:id,number', 'homeTeam:id,name', 'outsideTeam:id,name'])
            ->orderByDesc('date')
            ->get();
    }

    public function create(array $data): GameMatch
    {
        return GameMatch::create($data);
    }

    public function update(GameMatch $match, array $data): GameMatch
    {
        $match->update($data);

        return $match->fresh() ?? $match;
    }

    public function delete(GameMatch $match): bool
    {
        return (bool) $match->delete();
    }
}
