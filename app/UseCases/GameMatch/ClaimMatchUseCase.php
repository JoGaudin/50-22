<?php

namespace App\UseCases\GameMatch;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\GameMatch;
use App\Models\User;
use RuntimeException;

class ClaimMatchUseCase
{
    public function __construct(
        private readonly GameMatchRepositoryInterface $matches,
    ) {}

    public function execute(GameMatch $match, User $user): GameMatch
    {
        if ($match->referee_id !== null) {
            throw new RuntimeException(__('Ce match a déjà un arbitre.'));
        }

        return $this->matches->update($match, ['referee_id' => $user->id]);
    }
}
