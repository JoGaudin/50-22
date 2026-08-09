<?php

namespace App\UseCases\League;

use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Models\League;
use RuntimeException;

class DeleteLeagueUseCase
{
    public function __construct(
        private readonly LeagueRepositoryInterface $leagues,
    ) {}

    /**
     * @throws RuntimeException if the league still has seasons
     */
    public function execute(League $league): void
    {
        if ($league->seasons()->exists()) {
            throw new RuntimeException(__('Cette ligue a des saisons associées et ne peut pas être supprimée.'));
        }

        $this->leagues->delete($league);
    }
}
