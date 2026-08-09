<?php

namespace App\UseCases\Fiche;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Models\Fiche;

class CreateFicheUseCase
{
    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): Fiche
    {
        return $this->fiches->create($data);
    }
}
