<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\Journee;
use App\Models\Team;
use App\UseCases\GameMatch\CreateGameMatchUseCase;
use App\UseCases\GameMatch\DeleteGameMatchUseCase;
use App\UseCases\GameMatch\ListGameMatchesUseCase;
use App\UseCases\GameMatch\UpdateGameMatchUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class GameMatchManagementController extends Controller
{
    public function __construct(
        private readonly ListGameMatchesUseCase $list,
        private readonly CreateGameMatchUseCase $create,
        private readonly UpdateGameMatchUseCase $update,
        private readonly DeleteGameMatchUseCase $delete,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Matches/Index', [
            'matches' => $this->list->execute(),
            'journees' => Journee::query()->orderBy('number')->get(['id', 'number']),
            'teams' => Team::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $this->create->execute($validated);

        return redirect()->route('admin.matches.index')->with('success', __('Match créé.'));
    }

    public function update(Request $request, GameMatch $match): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $this->update->execute($match, $validated);

        return redirect()->route('admin.matches.index')->with('success', __('Match mis à jour.'));
    }

    public function destroy(GameMatch $match): RedirectResponse
    {
        try {
            $this->delete->execute($match);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.matches.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.matches.index')->with('success', __('Match supprimé.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'journee_id' => ['required', 'uuid', 'exists:journees,id'],
            'home_team_id' => ['required', 'uuid', 'exists:teams,id', 'different:outside_team_id'],
            'outside_team_id' => ['required', 'uuid', 'exists:teams,id'],
            'home_team_score' => ['nullable', 'integer', 'min:0'],
            'outside_team_score' => ['nullable', 'integer', 'min:0'],
            'referee_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['scheduled', 'played', 'cancelled'])],
        ];
    }
}
