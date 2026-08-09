<?php

namespace App\UseCases\League;

use App\Models\League;

class SyncLeagueRefereesUseCase
{
    /**
     * @param array<int, string> $userIds
     */
    public function execute(League $league, array $userIds): void
    {
        $league->referees()->sync($userIds);
    }
}
