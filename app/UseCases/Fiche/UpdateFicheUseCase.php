<?php

namespace App\UseCases\Fiche;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Models\Fiche;

class UpdateFicheUseCase
{
    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @param array<string, array{description: string}>|null $descriptions
     */
    public function execute(Fiche $fiche, array $data, ?array $descriptions = null): Fiche
    {
        $fiche = $this->fiches->update($fiche, $data);

        if ($descriptions !== null) {
            $fiche->paramDescriptions()->sync($descriptions);
        }

        return $fiche;
    }
}
