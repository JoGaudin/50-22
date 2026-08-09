<?php

namespace App\UseCases\Season;

use App\Contracts\Repositories\SeasonRepositoryInterface;
use App\Models\Season;

class UpdateSeasonUseCase
{
    public function __construct(
        private readonly SeasonRepositoryInterface $seasons,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(Season $season, array $data): Season
    {
        return $this->seasons->update($season, $data);
    }
}
