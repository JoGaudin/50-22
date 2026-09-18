<?php

namespace App\UseCases\League;

use App\Models\League;
use App\Models\User;

class LeaveLeagueUseCase
{
    public function execute(League $league, User $user): void
    {
        $league->referees()->detach($user->id);
    }
}
