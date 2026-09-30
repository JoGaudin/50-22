<?php

namespace App\UseCases\League;

use App\Models\League;
use App\Models\User;

class JoinLeagueUseCase
{
    public function execute(League $league, User $user): void
    {
        $league->referees()->syncWithoutDetaching([$user->id]);
    }
}
