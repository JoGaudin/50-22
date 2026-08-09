<?php

namespace App\UseCases\Fiche;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Models\Fiche;

class DeleteFicheUseCase
{
    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
    ) {}

    public function execute(Fiche $fiche): void
    {
        $this->fiches->delete($fiche);
    }
}
