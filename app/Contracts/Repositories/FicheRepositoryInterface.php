<?php

namespace App\Contracts\Repositories;

use App\Models\Fiche;
use App\Models\ParamDescription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface FicheRepositoryInterface
{
    public function findById(string $id): ?Fiche;

    /**
     * @return Collection<int, Fiche>
     */
    public function all(): Collection;

    /**
     * @return Collection<int, Fiche>
     */
    public function createdBy(User $user): Collection;

    public function latestForTeam(Team $team, User $user): ?Fiche;

    /**
     * @return Collection<int, Fiche>
     */
    public function versionsForTeam(Team $team, User $user): Collection;

    /**
     * @return Collection<int, Fiche>
     */
    public function recentAnswers(Team $team, ParamDescription $param, User $user, int $limit = 10): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Fiche;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Fiche $fiche, array $data): Fiche;

    public function delete(Fiche $fiche): bool;
}
