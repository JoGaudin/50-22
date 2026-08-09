<?php

namespace App\UseCases\League;

use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Models\League;
use Illuminate\Database\Eloquent\Collection;

class ListLeaguesUseCase
{
    public function __construct(
        private readonly LeagueRepositoryInterface $leagues,
    ) {}

    /**
     * @return Collection<int, League>
     */
    public function execute(): Collection
    {
        return $this->leagues->all();
    }
}
