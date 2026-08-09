<?php

namespace App\UseCases\League;

use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Models\League;

class UpdateLeagueUseCase
{
    public function __construct(
        private readonly LeagueRepositoryInterface $leagues,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(League $league, array $data): League
    {
        return $this->leagues->update($league, $data);
    }
}
