<?php

namespace App\Repositories;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Models\Fiche;
use App\Models\ParamDescription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentFicheRepository implements FicheRepositoryInterface
{
    public function findById(string $id): ?Fiche
    {
        return Fiche::find($id);
    }

    public function all(): Collection
    {
        return Fiche::query()->with(['team:id,name', 'creator:id,name'])->orderBy('name')->get();
    }

    public function createdBy(User $user): Collection
    {
        return Fiche::query()
            ->where('created_by', $user->id)
            ->with(['team:id,name', 'creator:id,name', 'paramDescriptions'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function latestForTeam(Team $team): ?Fiche
    {
        return Fiche::query()
            ->where('team_id', $team->id)
            ->orderByDesc('created_at')
            ->with('paramDescriptions')
            ->first();
    }

    public function versionsForTeam(Team $team): Collection
    {
        return Fiche::query()
            ->where('team_id', $team->id)
            ->orderByDesc('created_at')
            ->with(['creator:id,name', 'paramDescriptions'])
            ->get();
    }

    public function recentAnswers(Team $team, ParamDescription $param, int $limit = 10): Collection
    {
        return Fiche::query()
            ->where('team_id', $team->id)
            ->whereHas('paramDescriptions', fn ($q) => $q->where('param_descriptions.id', $param->id))
            ->with(['paramDescriptions' => fn ($q) => $q->where('param_descriptions.id', $param->id)])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function create(array $data): Fiche
    {
        return Fiche::create($data);
    }

    public function update(Fiche $fiche, array $data): Fiche
    {
        $fiche->update($data);

        return $fiche->fresh() ?? $fiche;
    }

    public function delete(Fiche $fiche): bool
    {
        return (bool) $fiche->delete();
    }
}
