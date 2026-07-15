<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Right;
use App\UseCases\Right\CreateRightUseCase;
use App\UseCases\Right\DeleteRightUseCase;
use App\UseCases\Right\ListRightsUseCase;
use App\UseCases\Right\UpdateRightUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class RightManagementController extends Controller
{
    public function __construct(
        private readonly ListRightsUseCase $listRights,
        private readonly CreateRightUseCase $createRight,
        private readonly UpdateRightUseCase $updateRight,
        private readonly DeleteRightUseCase $deleteRight,
    ) {}

    public function index(): Response
    {
        $rights = Right::query()
            ->withCount('roles')
            ->orderBy('name')
            ->get()
            ->map(fn (Right $right) => [
                'id' => $right->id,
                'name' => $right->name,
                'slug' => $right->slug,
                'description' => $right->description,
                'roles_count' => $right->roles_count,
                'is_system' => $right->slug === 'user',
            ]);

        return Inertia::render('Admin/Rights/Index', [
            'rights' => $rights,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9._-]+$/', Rule::unique('rights', 'slug')],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->createRight->execute(
            $validated['name'],
            $validated['slug'],
            $validated['description'] ?? null,
        );

        return redirect()->route('admin.rights.index')->with('success', __('Droit créé.'));
    }

    public function update(Request $request, Right $right): RedirectResponse
    {
        $slugLocked = $right->slug === 'user';

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];

        if (! $slugLocked) {
            $rules['slug'] = ['required', 'string', 'max:100', 'regex:/^[a-z0-9._-]+$/', Rule::unique('rights', 'slug')->ignore($right->id)];
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if (! $slugLocked) {
            $data['slug'] = $validated['slug'];
        }

        try {
            $this->updateRight->execute($right, $data);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.rights.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.rights.index')->with('success', __('Droit mis à jour.'));
    }

    public function destroy(Right $right): RedirectResponse
    {
        try {
            $this->deleteRight->execute($right);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.rights.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.rights.index')->with('success', __('Droit supprimé.'));
    }
}
