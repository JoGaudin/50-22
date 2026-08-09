<?php

namespace App\UseCases\GameMatch;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\GameMatch;

class CreateRefereeMatchUseCase
{
    public function __construct(
        private readonly GameMatchRepositoryInterface $matches,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): GameMatch
    {
        $data['status'] = 'scheduled';

        return $this->matches->create($data);
    }
}
