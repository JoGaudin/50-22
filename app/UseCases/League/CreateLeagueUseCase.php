<?php

namespace App\UseCases\League;

use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Models\League;

class CreateLeagueUseCase
{
    public function __construct(
        private readonly LeagueRepositoryInterface $leagues,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): League
    {
        return $this->leagues->create($data);
    }
}
