<?php

namespace App\Repositories;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Models\Fiche;
use App\Models\GameMatch;
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

    public function latestForTeam(Team $team, User $user): ?Fiche
    {
        return Fiche::query()
            ->where('team_id', $team->id)
            ->where('created_by', $user->id)
            ->orderByDesc('created_at')
            ->with('paramDescriptions')
            ->first();
    }

    public function versionsForTeam(Team $team, User $user): Collection
    {
        $fiches = Fiche::query()
            ->where('team_id', $team->id)
            ->where('created_by', $user->id)
            ->orderByDesc('created_at')
            ->with(['creator:id,name', 'paramDescriptions'])
            ->get();

        $ficheIds = $fiches->pluck('id');

        $matches = GameMatch::query()
            ->where(fn ($q) => $q->whereIn('home_fiche_id', $ficheIds))
            ->orWhere(fn ($q) => $q->whereIn('outside_fiche_id', $ficheIds))
            ->with(['journee:id,number', 'homeTeam:id,name', 'outsideTeam:id,name'])
            ->get(['id', 'home_fiche_id', 'outside_fiche_id', 'home_team_id', 'outside_team_id', 'journee_id', 'date']);

        $fiches->each(function (Fiche $fiche) use ($matches) {
            $match = $matches->first(
                fn (GameMatch $m) => $m->home_fiche_id === $fiche->id || $m->outside_fiche_id === $fiche->id,
            );

            $fiche->setAttribute('match', $match !== null ? [
                'id' => $match->id,
                'journee' => $match->journee !== null ? ['number' => $match->journee->number] : null,
                'home_team' => $match->homeTeam !== null ? ['name' => $match->homeTeam->name] : null,
                'outside_team' => $match->outsideTeam !== null ? ['name' => $match->outsideTeam->name] : null,
                'date' => $match->date,
            ] : null);
        });

        return $fiches;
    }

    public function recentAnswers(Team $team, ParamDescription $param, User $user, int $limit = 10): Collection
    {
        return Fiche::query()
            ->where('team_id', $team->id)
            ->where('created_by', $user->id)
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
