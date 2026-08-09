<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\User;
use App\UseCases\League\CreateLeagueUseCase;
use App\UseCases\League\DeleteLeagueUseCase;
use App\UseCases\League\ListLeaguesUseCase;
use App\UseCases\League\SyncLeagueRefereesUseCase;
use App\UseCases\League\UpdateLeagueUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class LeagueManagementController extends Controller
{
    public function __construct(
        private readonly ListLeaguesUseCase $list,
        private readonly CreateLeagueUseCase $create,
        private readonly UpdateLeagueUseCase $update,
        private readonly DeleteLeagueUseCase $delete,
        private readonly SyncLeagueRefereesUseCase $syncReferees,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Leagues/Index', [
            'leagues' => $this->list->execute()->load('referees:id,name'),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'referee_user_ids' => ['nullable', 'array'],
            'referee_user_ids.*' => ['uuid', 'exists:users,id'],
        ]);

        $refereeUserIds = $validated['referee_user_ids'] ?? [];
        unset($validated['referee_user_ids']);

        $league = $this->create->execute($validated);
        $this->syncReferees->execute($league, $refereeUserIds);

        return redirect()->route('admin.leagues.index')->with('success', __('Ligue créée.'));
    }

    public function update(Request $request, League $league): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'referee_user_ids' => ['nullable', 'array'],
            'referee_user_ids.*' => ['uuid', 'exists:users,id'],
        ]);

        $refereeUserIds = $validated['referee_user_ids'] ?? [];
        unset($validated['referee_user_ids']);

        $this->update->execute($league, $validated);
        $this->syncReferees->execute($league, $refereeUserIds);

        return redirect()->route('admin.leagues.index')->with('success', __('Ligue mise à jour.'));
    }

    public function destroy(League $league): RedirectResponse
    {
        try {
            $this->delete->execute($league);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.leagues.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.leagues.index')->with('success', __('Ligue supprimée.'));
    }
}
