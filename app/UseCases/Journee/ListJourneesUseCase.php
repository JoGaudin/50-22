<?php

namespace App\UseCases\Journee;

use App\Contracts\Repositories\JourneeRepositoryInterface;
use App\Models\Journee;
use Illuminate\Database\Eloquent\Collection;

class ListJourneesUseCase
{
    public function __construct(
        private readonly JourneeRepositoryInterface $journees,
    ) {}

    /**
     * @return Collection<int, Journee>
     */
    public function execute(): Collection
    {
        return $this->journees->all();
    }
}
