<?php

namespace App\UseCases\Right;

use App\Contracts\Repositories\RightRepositoryInterface;
use App\Models\Right;
use Illuminate\Database\Eloquent\Collection;

class ListRightsUseCase
{
    public function __construct(
        private readonly RightRepositoryInterface $rights,
    ) {}

    /**
     * @return Collection<int, Right>
     */
    public function execute(): Collection
    {
        return $this->rights->all();
    }
}
