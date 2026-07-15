<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\Setting\GetSettingUseCase;
use App\UseCases\Setting\UpdateSettingUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(
        private readonly GetSettingUseCase $getSetting,
        private readonly UpdateSettingUseCase $updateSetting,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Settings/Index', [
            'settings' => [
                '2fa_mode' => $this->getSetting->execute('2fa_mode', 'none'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'two_fa_mode' => ['required', 'string', 'in:none,email'],
        ]);

        $this->updateSetting->execute('2fa_mode', $request->string('two_fa_mode')->value());

        return back();
    }
}
