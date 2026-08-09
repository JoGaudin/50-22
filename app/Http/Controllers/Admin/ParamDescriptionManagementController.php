<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParamDescription;
use App\UseCases\ParamDescription\CreateParamDescriptionUseCase;
use App\UseCases\ParamDescription\DeleteParamDescriptionUseCase;
use App\UseCases\ParamDescription\ListParamDescriptionsUseCase;
use App\UseCases\ParamDescription\UpdateParamDescriptionUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ParamDescriptionManagementController extends Controller
{
    public function __construct(
        private readonly ListParamDescriptionsUseCase $list,
        private readonly CreateParamDescriptionUseCase $create,
        private readonly UpdateParamDescriptionUseCase $update,
        private readonly DeleteParamDescriptionUseCase $delete,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/ParamDescriptions/Index', [
            'paramDescriptions' => $this->list->execute(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ]);

        $this->create->execute($validated);

        return redirect()->route('admin.param-descriptions.index')->with('success', __('Paramètre créé.'));
    }

    public function update(Request $request, ParamDescription $paramDescription): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ]);

        $this->update->execute($paramDescription, $validated);

        return redirect()->route('admin.param-descriptions.index')->with('success', __('Paramètre mis à jour.'));
    }

    public function destroy(ParamDescription $paramDescription): RedirectResponse
    {
        try {
            $this->delete->execute($paramDescription);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.param-descriptions.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.param-descriptions.index')->with('success', __('Paramètre supprimé.'));
    }
}
