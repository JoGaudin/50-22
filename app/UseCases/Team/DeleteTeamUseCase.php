<?php

namespace App\UseCases\Team;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use RuntimeException;

class DeleteTeamUseCase
{
    public function __construct(
        private readonly TeamRepositoryInterface $teams,
    ) {}

    /**
     * @throws RuntimeException if the team still has fiches
     */
    public function execute(Team $team): void
    {
        if ($team->fiches()->exists()) {
            throw new RuntimeException(__('Cette équipe a des fiches associées et ne peut pas être supprimée.'));
        }

        $this->teams->delete($team);
    }
}
