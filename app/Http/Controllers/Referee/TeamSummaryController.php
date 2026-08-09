<?php

namespace App\Http\Controllers\Referee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Referee\Concerns\AuthorizesRefereeAccess;
use App\Models\Team;
use App\UseCases\TeamSummary\UpsertTeamSummaryUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamSummaryController extends Controller
{
    use AuthorizesRefereeAccess;

    public function store(Request $request, Team $team, UpsertTeamSummaryUseCase $upsert): RedirectResponse
    {
        $this->authorizeTeam($team);

        $validated = $request->validate([
            'description' => ['required', 'string'],
        ]);

        $upsert->execute($team, $request->user(), $validated['description']);

        return back()->with('success', __('Résumé enregistré.'));
    }
}
