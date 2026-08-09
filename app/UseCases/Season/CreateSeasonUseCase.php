<?php

namespace App\UseCases\Season;

use App\Contracts\Repositories\SeasonRepositoryInterface;
use App\Models\Season;

class CreateSeasonUseCase
{
    public function __construct(
        private readonly SeasonRepositoryInterface $seasons,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): Season
    {
        return $this->seasons->create($data);
    }
}
