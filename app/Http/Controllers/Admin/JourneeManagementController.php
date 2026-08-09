<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journee;
use App\Models\Season;
use App\UseCases\Journee\CreateJourneeUseCase;
use App\UseCases\Journee\DeleteJourneeUseCase;
use App\UseCases\Journee\ListJourneesUseCase;
use App\UseCases\Journee\UpdateJourneeUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class JourneeManagementController extends Controller
{
    public function __construct(
        private readonly ListJourneesUseCase $list,
        private readonly CreateJourneeUseCase $create,
        private readonly UpdateJourneeUseCase $update,
        private readonly DeleteJourneeUseCase $delete,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Journees/Index', [
            'journees' => $this->list->execute(),
            'seasons' => Season::query()->orderBy('start', 'desc')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'season_id' => ['required', 'uuid', 'exists:seasons,id'],
        ]);

        $this->create->execute($validated);

        return redirect()->route('admin.journees.index')->with('success', __('Journée créée.'));
    }

    public function update(Request $request, Journee $journee): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'season_id' => ['required', 'uuid', 'exists:seasons,id'],
        ]);

        $this->update->execute($journee, $validated);

        return redirect()->route('admin.journees.index')->with('success', __('Journée mise à jour.'));
    }

    public function destroy(Journee $journee): RedirectResponse
    {
        try {
            $this->delete->execute($journee);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.journees.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.journees.index')->with('success', __('Journée supprimée.'));
    }
}
