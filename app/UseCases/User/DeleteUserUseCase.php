<?php

namespace App\UseCases\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use RuntimeException;

class DeleteUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    /**
     * @throws RuntimeException if the actor tries to delete themselves
     */
    public function execute(User $user, int $actorId): void
    {
        if ($user->id === $actorId) {
            throw new RuntimeException(__('Vous ne pouvez pas supprimer votre propre compte.'));
        }

        $this->users->delete($user);
    }
}
