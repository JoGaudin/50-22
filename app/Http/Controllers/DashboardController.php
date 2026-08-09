<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Models\ParamDescription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
        private readonly GameMatchRepositoryInterface $matches,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $mine = $this->matches->forReferee($user);
        $today = now()->toDateString();

        return Inertia::render('Dashboard', [
            'recentFiches' => $this->fiches->createdBy($user),
            'paramDescriptions' => ParamDescription::query()->orderBy('order')->orderBy('name')->get(['id', 'name']),
            'upcomingMatches' => $mine->where('date', '>=', $today)->sortBy('date')->take(5)->values(),
            'pastMatches' => $mine->where('date', '<', $today)->sortByDesc('date')->take(5)->values(),
        ]);
    }
}
