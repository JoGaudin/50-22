<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TwoFactorChallengeController extends Controller
{
    /**
     * Show the 2FA challenge form.
     * Only accessible when two_factor_login_user_id is in session (pending login).
     */
    public function create(Request $request): Response|RedirectResponse
    {
        if (! $request->session()->has('two_factor_login_user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * Verify the email code and complete the login.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $userId = $request->session()->get('two_factor_login_user_id');
        $remember = $request->session()->get('two_factor_login_remember', false);
        $storedCode = $request->session()->get('two_factor_login_code');
        $expiresAt = $request->session()->get('two_factor_login_code_expires_at');

        if (! $userId || ! $storedCode) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget([
                'two_factor_login_user_id',
                'two_factor_login_remember',
                'two_factor_login_code',
                'two_factor_login_code_expires_at',
            ]);

            return redirect()->route('login');
        }

        $enteredCode = $request->string('code')->replace(' ', '')->value();

        if ($expiresAt && now()->timestamp > $expiresAt) {
            return back()->withErrors(['code' => 'Ce code a expiré. Veuillez vous reconnecter pour recevoir un nouveau code.']);
        }

        if (! hash_equals($storedCode, $enteredCode)) {
            return back()->withErrors(['code' => 'Code invalide. Veuillez réessayer.']);
        }

        $request->session()->forget([
            'two_factor_login_user_id',
            'two_factor_login_remember',
            'two_factor_login_code',
            'two_factor_login_code_expires_at',
        ]);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
