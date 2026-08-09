<?php

namespace App\UseCases\Season;

use App\Contracts\Repositories\SeasonRepositoryInterface;
use App\Models\Season;

class DeleteSeasonUseCase
{
    public function __construct(
        private readonly SeasonRepositoryInterface $seasons,
    ) {}

    public function execute(Season $season): void
    {
        $this->seasons->delete($season);
    }
}
