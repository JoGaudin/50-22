<?php

namespace App\UseCases\GameMatch;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\Fiche;
use App\Models\GameMatch;

class LinkMatchFicheUseCase
{
    public function __construct(
        private readonly GameMatchRepositoryInterface $matches,
    ) {}

    public function execute(GameMatch $match, string $side, ?Fiche $fiche): GameMatch
    {
        $column = $side === 'home' ? 'home_fiche_id' : 'outside_fiche_id';

        return $this->matches->update($match, [$column => $fiche?->id]);
    }
}
