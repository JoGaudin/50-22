<?php

namespace App\Repositories;

use App\Contracts\Repositories\TeamSummaryRepositoryInterface;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentTeamSummaryRepository implements TeamSummaryRepositoryInterface
{
    public function forTeam(Team $team): Collection
    {
        return $team->users()->orderBy('name')->get();
    }

    public function upsert(Team $team, User $user, string $description): void
    {
        $team->users()->syncWithoutDetaching([$user->id => ['description' => $description]]);
    }
}
