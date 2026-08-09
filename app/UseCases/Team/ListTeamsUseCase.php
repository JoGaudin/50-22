<?php

namespace App\UseCases\Team;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class ListTeamsUseCase
{
    public function __construct(
        private readonly TeamRepositoryInterface $teams,
    ) {}

    /**
     * @return Collection<int, Team>
     */
    public function execute(): Collection
    {
        return $this->teams->all();
    }
}
