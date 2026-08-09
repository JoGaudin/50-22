<?php

namespace App\UseCases\Team;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;

class CreateTeamUseCase
{
    public function __construct(
        private readonly TeamRepositoryInterface $teams,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): Team
    {
        return $this->teams->create($data);
    }
}
