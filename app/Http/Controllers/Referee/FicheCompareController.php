<?php

namespace App\Http\Controllers\Referee;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Referee\Concerns\AuthorizesRefereeAccess;
use App\Models\Fiche;
use App\Models\ParamDescription;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FicheCompareController extends Controller
{
    use AuthorizesRefereeAccess;

    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
    ) {}

    public function index(Request $request): Response
    {
        $teams = Team::query()
            ->whereHas('seasons.league.referees', fn ($q) => $q->where('users.id', Auth::id()))
            ->orderBy('name')
            ->get(['id', 'name']);

        $team = null;
        $fiches = collect();

        if ($teamId = $request->query('team_id')) {
            $team = Team::find($teamId);

            if ($team !== null) {
                $this->authorizeTeam($team);

                $fiches = $this->fiches->versionsForTeam($team, $request->user());
            }
        }

        return Inertia::render('Referee/Fiches/Compare', [
            'teams' => $teams,
            'team' => $team,
            'fiches' => $fiches,
            'paramDescriptions' => ParamDescription::query()->orderBy('order')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function answers(Request $request, Team $team, ParamDescription $paramDescription): JsonResponse
    {
        $this->authorizeTeam($team);

        $fiches = $this->fiches->recentAnswers($team, $paramDescription, $request->user());

        return response()->json($fiches->map(fn (Fiche $fiche) => [
            'fiche_name' => $fiche->name,
            'description' => $fiche->paramDescriptions->first()?->pivot->description,
            'created_at' => $fiche->created_at,
        ]));
    }
}
