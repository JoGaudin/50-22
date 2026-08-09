<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fiche;
use App\Models\Team;
use App\UseCases\Fiche\CreateFicheUseCase;
use App\UseCases\Fiche\DeleteFicheUseCase;
use App\UseCases\Fiche\ListFichesUseCase;
use App\UseCases\Fiche\UpdateFicheUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class FicheManagementController extends Controller
{
    public function __construct(
        private readonly ListFichesUseCase $list,
        private readonly CreateFicheUseCase $create,
        private readonly UpdateFicheUseCase $update,
        private readonly DeleteFicheUseCase $delete,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Fiches/Index', [
            'fiches' => $this->list->execute(),
            'teams' => Team::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'team_id' => ['required', 'uuid', 'exists:teams,id'],
        ]);

        $validated['created_by'] = $request->user()?->id;

        $this->create->execute($validated);

        return redirect()->route('admin.fiches.index')->with('success', __('Fiche créée.'));
    }

    public function update(Request $request, Fiche $fiche): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'team_id' => ['required', 'uuid', 'exists:teams,id'],
        ]);

        $this->update->execute($fiche, $validated);

        return redirect()->route('admin.fiches.index')->with('success', __('Fiche mise à jour.'));
    }

    public function destroy(Fiche $fiche): RedirectResponse
    {
        try {
            $this->delete->execute($fiche);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.fiches.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.fiches.index')->with('success', __('Fiche supprimée.'));
    }
}
