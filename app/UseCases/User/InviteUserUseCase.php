<?php

namespace App\UseCases\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use RuntimeException;

class InviteUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    /**
     * @throws RuntimeException if the user already has a password set
     */
    public function execute(User $user): void
    {
        if ($user->password !== null) {
            throw new RuntimeException(__('Ce compte a déjà un mot de passe.'));
        }

        $user->notify(new UserInvitationNotification);
    }
}
