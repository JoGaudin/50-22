<?php

namespace App\Repositories;

use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Models\League;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentLeagueRepository implements LeagueRepositoryInterface
{
    public function findById(string $id): ?League
    {
        return League::find($id);
    }

    public function all(): Collection
    {
        return League::query()->orderBy('name')->get();
    }

    public function forUser(User $user): Collection
    {
        return League::query()
            ->whereHas('referees', fn ($q) => $q->where('users.id', $user->id))
            ->orderBy('name')
            ->get();
    }

    public function availableForUser(User $user): Collection
    {
        return League::query()
            ->whereDoesntHave('referees', fn ($q) => $q->where('users.id', $user->id))
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): League
    {
        return League::create($data);
    }

    public function update(League $league, array $data): League
    {
        $league->update($data);

        return $league->fresh() ?? $league;
    }

    public function delete(League $league): bool
    {
        return (bool) $league->delete();
    }
}
