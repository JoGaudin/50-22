<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Season;
use App\UseCases\Season\CreateSeasonUseCase;
use App\UseCases\Season\DeleteSeasonUseCase;
use App\UseCases\Season\ListSeasonsUseCase;
use App\UseCases\Season\UpdateSeasonUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class SeasonManagementController extends Controller
{
    public function __construct(
        private readonly ListSeasonsUseCase $list,
        private readonly CreateSeasonUseCase $create,
        private readonly UpdateSeasonUseCase $update,
        private readonly DeleteSeasonUseCase $delete,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Seasons/Index', [
            'seasons' => $this->list->execute(),
            'leagues' => League::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'league_id' => ['required', 'uuid', 'exists:leagues,id'],
        ]);

        $this->create->execute($validated);

        return redirect()->route('admin.seasons.index')->with('success', __('Saison créée.'));
    }

    public function update(Request $request, Season $season): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'league_id' => ['required', 'uuid', 'exists:leagues,id'],
        ]);

        $this->update->execute($season, $validated);

        return redirect()->route('admin.seasons.index')->with('success', __('Saison mise à jour.'));
    }

    public function destroy(Season $season): RedirectResponse
    {
        try {
            $this->delete->execute($season);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.seasons.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.seasons.index')->with('success', __('Saison supprimée.'));
    }
}
