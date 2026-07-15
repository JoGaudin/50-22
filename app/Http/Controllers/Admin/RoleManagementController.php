<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Right;
use App\Models\Role;
use App\UseCases\Role\CreateRoleUseCase;
use App\UseCases\Role\DeleteRoleUseCase;
use App\UseCases\Role\ListRolesUseCase;
use App\UseCases\Role\UpdateRoleUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class RoleManagementController extends Controller
{
    public function __construct(
        private readonly ListRolesUseCase $listRoles,
        private readonly CreateRoleUseCase $createRole,
        private readonly UpdateRoleUseCase $updateRole,
        private readonly DeleteRoleUseCase $deleteRole,
    ) {}

    public function index(): Response
    {
        $roles = $this->listRoles->execute()->map(fn (Role $role) => [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'right_ids' => $role->rights->pluck('id')->values()->all(),
            'rights' => $role->rights->map(fn (Right $right) => [
                'id' => $right->id,
                'name' => $right->name,
                'slug' => $right->slug,
            ]),
            'is_system' => in_array($role->slug, ['admin', 'user'], true),
        ]);

        $rights = Right::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'rights' => $rights,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9._-]+$/', Rule::unique('roles', 'slug')],
            'description' => ['nullable', 'string', 'max:2000'],
            'right_ids' => ['nullable', 'array'],
            'right_ids.*' => ['integer', 'exists:rights,id'],
        ]);

        $role = $this->createRole->execute(
            $validated['name'],
            $validated['slug'],
            $validated['description'] ?? null,
        );

        $role->rights()->sync($validated['right_ids'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', __('Rôle créé.'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $isAdminRole = $role->slug === 'admin';
        $isUserRole = $role->slug === 'user';
        $slugLocked = $isAdminRole || $isUserRole;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];

        if ($slugLocked) {
            $rules['right_ids'] = $isAdminRole
                ? ['nullable', 'array']
                : ['required', 'array', 'min:1'];
            $rules['right_ids.*'] = ['integer', 'exists:rights,id'];
        } else {
            $rules['slug'] = ['required', 'string', 'max:100', 'regex:/^[a-z0-9._-]+$/', Rule::unique('roles', 'slug')->ignore($role->id)];
            $rules['right_ids'] = ['required', 'array'];
            $rules['right_ids.*'] = ['integer', 'exists:rights,id'];
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if (! $slugLocked) {
            $data['slug'] = $validated['slug'];
        }

        $this->updateRole->execute($role, $data);

        if ($isAdminRole) {
            $role->rights()->sync(Right::query()->pluck('id')->all());
        } else {
            $role->rights()->sync($validated['right_ids'] ?? []);
        }

        return redirect()->route('admin.roles.index')->with('success', __('Rôle mis à jour.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->deleteRole->execute($role);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.roles.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.roles.index')->with('success', __('Rôle supprimé.'));
    }
}
