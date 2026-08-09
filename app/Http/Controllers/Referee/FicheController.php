<?php

namespace App\Http\Controllers\Referee;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Referee\Concerns\AuthorizesRefereeAccess;
use App\Models\Fiche;
use App\Models\Team;
use App\UseCases\Fiche\CreateFicheVersionUseCase;
use App\UseCases\Fiche\UpdateFicheUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FicheController extends Controller
{
    use AuthorizesRefereeAccess;

    public function __construct(
        private readonly FicheRepositoryInterface $fiches,
        private readonly CreateFicheVersionUseCase $createVersion,
        private readonly UpdateFicheUseCase $update,
    ) {}

    public function index(Team $team): RedirectResponse
    {
        $this->authorizeTeam($team);

        $league = $team->seasons()->latest('start')->first()?->league;

        abort_unless($league, 404);

        return redirect()->route('referee.leagues.show', $league);
    }

    public function store(Request $request, Team $team): RedirectResponse
    {
        $this->authorizeTeam($team);

        $validated = $this->validateFiche($request);

        $this->createVersion->execute(
            $team,
            [
                'name' => $validated['name'],
                'created_by' => $request->user()->id,
            ],
            $validated['descriptions'],
        );

        return back()->with('success', __('Fiche créée.'));
    }

    public function update(Request $request, Fiche $fiche): RedirectResponse
    {
        $this->authorizeFiche($fiche);
        $this->abortUnlessLatest($fiche);

        $validated = $this->validateFiche($request);

        $this->update->execute(
            $fiche,
            ['name' => $validated['name']],
            $this->syncPayload($validated['descriptions']),
        );

        return back()->with('success', __('Fiche mise à jour.'));
    }

    /**
     * @return array{name: string, descriptions: array<int, array{param_description_id: string, description: string}>}
     */
    private function validateFiche(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'descriptions' => ['required', 'array'],
            'descriptions.*.param_description_id' => ['required', 'uuid', 'exists:param_descriptions,id'],
            'descriptions.*.description' => ['nullable', 'string'],
        ]);
    }

    /**
     * @param array<int, array{param_description_id: string, description: string}> $descriptions
     * @return array<string, array{description: string}>
     */
    private function syncPayload(array $descriptions): array
    {
        return collect($descriptions)
            ->mapWithKeys(fn (array $row) => [$row['param_description_id'] => ['description' => $row['description'] ?? '']])
            ->all();
    }

    private function abortUnlessLatest(Fiche $fiche): void
    {
        $latest = $this->fiches->latestForTeam($fiche->team);

        abort_unless($latest && $fiche->is($latest), 404);
    }
}
