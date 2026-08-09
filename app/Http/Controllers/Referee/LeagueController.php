<?php

namespace App\Http\Controllers\Referee;

use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Referee\Concerns\AuthorizesRefereeAccess;
use App\Models\League;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeagueController extends Controller
{
    use AuthorizesRefereeAccess;

    public function __construct(
        private readonly LeagueRepositoryInterface $leagues,
    ) {}

    public function index(Request $request): Response|RedirectResponse
    {
        $leagues = $this->leagues->forUser($request->user());

        if ($leagues->count() === 1) {
            return redirect()->route('referee.leagues.show', $leagues->first());
        }

        return Inertia::render('Referee/Leagues/Index', [
            'leagues' => $leagues,
        ]);
    }

    public function show(League $league): Response
    {
        $this->authorizeLeague($league);

        $season = $league->currentSeason();

        return Inertia::render('Referee/Leagues/Show', [
            'league' => $league,
            'season' => $season,
            'teams' => $season?->teams()->orderBy('name')->get(['teams.id', 'name', 'slug', 'logo', 'ville']) ?? [],
            'matches' => $season?->matches()
                ->with(['homeTeam:id,name', 'outsideTeam:id,name', 'journee:id,number', 'referee:id,name'])
                ->orderByDesc('date')
                ->get() ?? [],
            'journees' => $season?->journees()->orderBy('number')->get(['id', 'number', 'start_date', 'end_date']) ?? [],
        ]);
    }
}
