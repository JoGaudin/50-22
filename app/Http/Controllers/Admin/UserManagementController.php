<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\UseCases\User\CreateUserUseCase;
use App\UseCases\User\DeleteUserUseCase;
use App\UseCases\User\InviteUserUseCase;
use App\UseCases\User\ListUsersUseCase;
use App\UseCases\User\SyncUserRolesUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class UserManagementController extends Controller
{
    public function __construct(
        private readonly ListUsersUseCase $listUsers,
        private readonly CreateUserUseCase $createUser,
        private readonly InviteUserUseCase $inviteUser,
        private readonly SyncUserRolesUseCase $syncUserRoles,
        private readonly DeleteUserUseCase $deleteUser,
    ) {}

    public function index(Request $request): Response|JsonResponse
    {
        $validated = $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', Rule::in(['name', 'email', 'created_at'])],
            'sort_dir' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $usersData = $this->listUsers->execute($validated);

        if ($request->wantsJson()) {
            return response()->json($usersData);
        }

        $allRoles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Admin/Users/Index', [
            'usersData' => $usersData,
            'allRoles' => $allRoles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
        ]);

        $this->createUser->execute($validated['name'], $validated['email']);

        return redirect()->route('admin.users.index')->with('success', __('Utilisateur invité.'));
    }

    public function invite(User $user): RedirectResponse
    {
        try {
            $this->inviteUser->execute($user);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.users.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', __('Invitation envoyée.'));
    }

    public function syncRoles(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        $this->syncUserRoles->execute($user, $validated['role_ids']);

        return redirect()->route('admin.users.index')->with('success', __('Rôles mis à jour.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        try {
            $this->deleteUser->execute($user, (int) $request->user()?->id);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.users.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', __('Utilisateur supprimé.'));
    }
}
