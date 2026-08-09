<?php

namespace App\UseCases\Fiche;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Models\Fiche;
use App\Models\Team;

class CreateFicheVersionUseCase
{
    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
    ) {}

    /**
     * @param array<string, mixed> $attributes
     * @param array<int, array{param_description_id: string, description: string}> $descriptions
     */
    public function execute(Team $team, array $attributes, array $descriptions): Fiche
    {
        $attributes['team_id'] = $team->id;

        $fiche = $this->fiches->create($attributes);
        $fiche->paramDescriptions()->sync(
            collect($descriptions)->mapWithKeys(
                fn (array $row) => [$row['param_description_id'] => ['description' => $row['description'] ?? '']],
            )->all(),
        );

        return $fiche;
    }
}
