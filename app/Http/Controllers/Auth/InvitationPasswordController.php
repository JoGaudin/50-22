<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class InvitationPasswordController extends Controller
{
    public function show(User $user): RedirectResponse|Response
    {
        if ($user->password !== null) {
            return redirect()->route('login')->with('status', __('Ce compte est déjà actif. Connectez-vous.'));
        }

        return Inertia::render('Auth/AcceptInvitation', [
            'email' => $user->email,
            'submitUrl' => URL::temporarySignedRoute(
                'invitation.accept.store',
                now()->addDays(7),
                ['user' => $user->id]
            ),
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        if ($user->password !== null) {
            abort(403);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->forceFill([
            'password' => $validated['password'],
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        return redirect()->route('login')->with('status', __('Mot de passe enregistré. Vous pouvez vous connecter.'));
    }
}
