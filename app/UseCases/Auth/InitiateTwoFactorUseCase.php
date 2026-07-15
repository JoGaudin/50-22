<?php

namespace App\UseCases\Auth;

use App\Contracts\Repositories\SettingRepositoryInterface;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InitiateTwoFactorUseCase
{
    public function __construct(
        private readonly SettingRepositoryInterface $settings,
    ) {}

    /**
     * Check if 2FA is required and, if so, store the pending login in session,
     * send the code and return true. Returns false if no 2FA is needed.
     */
    public function execute(Request $request, User $user, bool $remember): bool
    {
        if ($this->settings->get('2fa_mode', 'none') !== 'email') {
            return false;
        }

        $code = (string) random_int(100000, 999999);

        Auth::logout();

        $request->session()->put('two_factor_login_user_id', $user->id);
        $request->session()->put('two_factor_login_remember', $remember);
        $request->session()->put('two_factor_login_code', $code);
        $request->session()->put('two_factor_login_code_expires_at', now()->addMinutes(5)->timestamp);

        $user->notify(new TwoFactorCodeNotification($code));

        return true;
    }
}
