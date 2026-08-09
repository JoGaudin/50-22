<?php

namespace App\UseCases\GameMatch;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\GameMatch;
use Illuminate\Database\Eloquent\Collection;

class ListGameMatchesUseCase
{
    public function __construct(
        private readonly GameMatchRepositoryInterface $matches,
    ) {}

    /**
     * @return Collection<int, GameMatch>
     */
    public function execute(): Collection
    {
        return $this->matches->all();
    }
}
