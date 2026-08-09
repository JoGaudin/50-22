<?php

namespace App\Contracts\Repositories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TeamSummaryRepositoryInterface
{
    /** @return Collection<int, User> */
    public function forTeam(Team $team): Collection;

    public function upsert(Team $team, User $user, string $description): void;
}
