<?php

namespace App\UseCases\GameMatch;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\GameMatch;

class UpdateGameMatchUseCase
{
    public function __construct(
        private readonly GameMatchRepositoryInterface $matches,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(GameMatch $match, array $data): GameMatch
    {
        return $this->matches->update($match, $data);
    }
}
