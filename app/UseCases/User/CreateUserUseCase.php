<?php

namespace App\UseCases\User;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Notifications\UserInvitationNotification;

class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly RoleRepositoryInterface $roles,
    ) {}

    public function execute(string $name, string $email): User
    {
        $user = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => null,
        ]);

        $userRole = $this->roles->findBySlug('user');
        if ($userRole !== null) {
            $this->users->syncRoles($user, [$userRole->id]);
        }

        $user->notify(new UserInvitationNotification);

        return $user;
    }
}
