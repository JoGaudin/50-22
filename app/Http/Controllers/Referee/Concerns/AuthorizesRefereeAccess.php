<?php

namespace App\Http\Controllers\Referee\Concerns;

use App\Models\Fiche;
use App\Models\GameMatch;
use App\Models\League;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;

trait AuthorizesRefereeAccess
{
    protected function authorizeLeague(League $league): void
    {
        abort_unless(
            $league->referees()->where('users.id', Auth::id())->exists(),
            403,
        );
    }

    protected function authorizeTeam(Team $team): void
    {
        abort_unless(
            $team->seasons()->whereHas('league.referees', fn ($q) => $q->where('users.id', Auth::id()))->exists(),
            403,
        );
    }

    protected function authorizeFiche(Fiche $fiche): void
    {
        $this->authorizeTeam($fiche->team);

        abort_unless($fiche->created_by === Auth::id(), 403);
    }

    protected function authorizeMatch(GameMatch $match): void
    {
        $match->loadMissing('journee.season.league.referees');

        abort_unless(
            $match->journee->season->league->referees->contains('id', Auth::id()),
            403,
        );
    }
}
