<?php

namespace App\Http\Controllers\Referee;

use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Referee\Concerns\AuthorizesRefereeAccess;
use App\Models\GameMatch;
use App\Models\League;
use App\Models\ParamDescription;
use App\UseCases\Fiche\CreateFicheVersionUseCase;
use App\UseCases\GameMatch\ClaimMatchUseCase;
use App\UseCases\GameMatch\CreateRefereeMatchUseCase;
use App\UseCases\GameMatch\LinkMatchFicheUseCase;
use App\UseCases\GameMatch\UpdateGameMatchUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class MatchController extends Controller
{
    use AuthorizesRefereeAccess;

    public function __construct(
        private readonly CreateRefereeMatchUseCase $create,
        private readonly GameMatchRepositoryInterface $matches,
        private readonly LeagueRepositoryInterface $leagues,
    ) {}

    public function index(Request $request): Response
    {
        $leagues = $this->leagues->forUser($request->user())->map(function (League $league) {
            $season = $league->currentSeason();

            return [
                'id' => $league->id,
                'name' => $league->name,
                'teams' => $season?->teams()->orderBy('name')->get(['teams.id', 'name']) ?? [],
                'journees' => $season?->journees()->orderBy('number')->get(['id', 'number']) ?? [],
            ];
        })->values();

        return Inertia::render('Referee/Matches/Index', [
            'matches' => $this->matches->forLeaguesOfUser($request->user()),
            'leagues' => $leagues,
        ]);
    }

    public function store(Request $request, League $league): RedirectResponse
    {
        $this->authorizeLeague($league);

        $season = $league->currentSeason();
        abort_if($season === null, 422, __('Aucune saison en cours pour ce championnat.'));

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'journee_id' => ['required', 'uuid', Rule::exists('journees', 'id')->where('season_id', $season->id)],
            'home_team_id' => [
                'required', 'uuid', 'different:outside_team_id',
                Rule::exists('season_team', 'team_id')->where('season_id', $season->id),
            ],
            'outside_team_id' => ['required', 'uuid', Rule::exists('season_team', 'team_id')->where('season_id', $season->id)],
            'referee_name' => ['nullable', 'string', 'max:255'],
            'as_referee' => ['sometimes', 'boolean'],
        ]);

        if (! empty($validated['as_referee'])) {
            $validated['referee_id'] = $request->user()->id;
        }
        unset($validated['as_referee']);

        $validated['created_by'] = $request->user()->id;

        $this->create->execute($validated);

        return back()->with('success', __('Match créé.'));
    }

    public function show(GameMatch $match): Response
    {
        $this->authorizeMatch($match);

        $match->load([
            'homeTeam', 'outsideTeam', 'journee.season.league', 'referee:id,name',
            'homeFiche.paramDescriptions', 'outsideFiche.paramDescriptions',
        ]);

        return Inertia::render('Referee/Matches/Show', [
            'match' => $match,
            'homeFiche' => $match->homeFiche,
            'outsideFiche' => $match->outsideFiche,
            'paramDescriptions' => ParamDescription::query()->orderBy('order')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, GameMatch $match, UpdateGameMatchUseCase $update): RedirectResponse
    {
        $this->authorizeMatch($match);

        $validated = $request->validate([
            'home_team_score' => ['nullable', 'integer', 'min:0'],
            'outside_team_score' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['scheduled', 'played', 'cancelled'])],
        ]);

        $update->execute($match, $validated);

        return redirect()->route('referee.matches.show', $match)->with('success', __('Match mis à jour.'));
    }

    public function claim(Request $request, GameMatch $match, ClaimMatchUseCase $claim): RedirectResponse
    {
        $this->authorizeMatch($match);

        try {
            $claim->execute($match, $request->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('Vous êtes désormais arbitre de ce match.'));
    }

    public function createFiche(
        Request $request,
        GameMatch $match,
        CreateFicheVersionUseCase $createVersion,
        LinkMatchFicheUseCase $link,
    ): RedirectResponse {
        $this->authorizeMatch($match);

        $validated = $request->validate([
            'fiches' => ['required', 'array', 'size:2'],
            'fiches.*.side' => ['required', 'distinct', Rule::in(['home', 'outside'])],
            'fiches.*.name' => ['required', 'string', 'max:255'],
            'fiches.*.descriptions' => ['required', 'array'],
            'fiches.*.descriptions.*.param_description_id' => ['required', 'uuid', 'exists:param_descriptions,id'],
            'fiches.*.descriptions.*.description' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $request, $match, $createVersion, $link): void {
            foreach ($validated['fiches'] as $entry) {
                $team = $entry['side'] === 'home' ? $match->homeTeam : $match->outsideTeam;

                $fiche = $createVersion->execute(
                    $team,
                    [
                        'name' => $entry['name'],
                        'created_by' => $request->user()->id,
                    ],
                    $entry['descriptions'],
                );

                $link->execute($match, $entry['side'], $fiche);
            }
        });

        return redirect()->route('referee.matches.show', $match)->with('success', __('Fiches créées et liées.'));
    }

    public function exportFiches(GameMatch $match): HttpResponse
    {
        $this->authorizeMatch($match);

        $match->load(['homeTeam', 'outsideTeam', 'homeFiche.paramDescriptions', 'outsideFiche.paramDescriptions']);

        abort_if($match->homeFiche === null || $match->outsideFiche === null, 404);

        $paramDescriptions = ParamDescription::query()->orderBy('order')->orderBy('name')->get(['id', 'name']);

        return Pdf::loadView('pdf.fiches-compare', ['match' => $match, 'paramDescriptions' => $paramDescriptions])
            ->download("fiches-{$match->id}.pdf");
    }
}
