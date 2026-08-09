<?php

namespace App\UseCases\GameMatch;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\GameMatch;

class DeleteGameMatchUseCase
{
    public function __construct(
        private readonly GameMatchRepositoryInterface $matches,
    ) {}

    public function execute(GameMatch $match): void
    {
        $this->matches->delete($match);
    }
}
