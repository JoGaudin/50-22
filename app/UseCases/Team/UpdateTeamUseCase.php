<?php

namespace App\UseCases\Team;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;

class UpdateTeamUseCase
{
    public function __construct(
        private readonly TeamRepositoryInterface $teams,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(Team $team, array $data): Team
    {
        return $this->teams->update($team, $data);
    }
}
