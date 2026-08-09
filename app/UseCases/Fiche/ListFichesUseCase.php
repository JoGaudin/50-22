<?php

namespace App\UseCases\Fiche;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Models\Fiche;
use Illuminate\Database\Eloquent\Collection;

class ListFichesUseCase
{
    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
    ) {}

    /**
     * @return Collection<int, Fiche>
     */
    public function execute(): Collection
    {
        return $this->fiches->all();
    }
}
