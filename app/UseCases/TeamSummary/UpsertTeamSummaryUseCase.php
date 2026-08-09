<?php

namespace App\UseCases\TeamSummary;

use App\Contracts\Repositories\TeamSummaryRepositoryInterface;
use App\Models\Team;
use App\Models\User;

class UpsertTeamSummaryUseCase
{
    public function __construct(
        private readonly TeamSummaryRepositoryInterface $summaries,
    ) {}

    public function execute(Team $team, User $user, string $description): void
    {
        $this->summaries->upsert($team, $user, $description);
    }
}
