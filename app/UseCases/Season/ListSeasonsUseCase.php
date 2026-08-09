<?php

namespace App\UseCases\Season;

use App\Contracts\Repositories\SeasonRepositoryInterface;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

class ListSeasonsUseCase
{
    public function __construct(
        private readonly SeasonRepositoryInterface $seasons,
    ) {}

    /**
     * @return Collection<int, Season>
     */
    public function execute(): Collection
    {
        return $this->seasons->all();
    }
}
