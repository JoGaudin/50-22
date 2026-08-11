<?php

namespace App\Http\Controllers\Referee;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Contracts\Repositories\TeamSummaryRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Referee\Concerns\AuthorizesRefereeAccess;
use App\Models\ParamDescription;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamPanelController extends Controller
{
    use AuthorizesRefereeAccess;

    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
        private readonly TeamSummaryRepositoryInterface $summaries,
    ) {}

    public function show(Request $request, Team $team): JsonResponse
    {
        $this->authorizeTeam($team);

        return response()->json([
            'versions' => $this->fiches->versionsForTeam($team, $request->user()),
            'latestFiche' => $this->fiches->latestForTeam($team, $request->user()),
            'summaries' => $this->summaries->forTeam($team),
            'paramDescriptions' => ParamDescription::query()->orderBy('order')->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
