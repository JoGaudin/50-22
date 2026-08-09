<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\UseCases\Team\CreateTeamUseCase;
use App\UseCases\Team\DeleteTeamUseCase;
use App\UseCases\Team\ListTeamsUseCase;
use App\UseCases\Team\UpdateTeamUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class TeamManagementController extends Controller
{
    public function __construct(
        private readonly ListTeamsUseCase $list,
        private readonly CreateTeamUseCase $create,
        private readonly UpdateTeamUseCase $update,
        private readonly DeleteTeamUseCase $delete,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Teams/Index', [
            'teams' => $this->list->execute(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('teams', 'slug')],
            'logo' => ['nullable', 'image', 'max:4096'],
            'stade' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('team-logos', 'public');
        } else {
            unset($validated['logo']);
        }

        $this->create->execute($validated);

        return redirect()->route('admin.teams.index')->with('success', __('Équipe créée.'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('teams', 'slug')->ignore($team->id)],
            'logo' => ['nullable', 'image', 'max:4096'],
            'stade' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('team-logos', 'public');
        } else {
            unset($validated['logo']);
        }

        $this->update->execute($team, $validated);

        return redirect()->route('admin.teams.index')->with('success', __('Équipe mise à jour.'));
    }

    public function destroy(Team $team): RedirectResponse
    {
        try {
            $this->delete->execute($team);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.teams.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.teams.index')->with('success', __('Équipe supprimée.'));
    }
}
