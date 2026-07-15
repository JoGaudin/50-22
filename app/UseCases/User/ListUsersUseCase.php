<?php

namespace App\UseCases\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\Role;

class ListUsersUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    /**
     * @param array{
     *     search?: string,
     *     sort_by?: string,
     *     sort_dir?: string,
     *     limit?: int,
     *     page?: int,
     * } $filters
     * @return array{
     *     data: list<array{
     *         id: int,
     *         name: string,
     *         email: string,
     *         password_set: bool,
     *         created_at: string|null,
     *         role_ids: list<int>,
     *         roles: list<array{id: int, name: string, slug: string}>
     *     }>,
     *     meta: array{current_page: int, last_page: int, per_page: int, total: int, has_more_pages: bool},
     *     sort: array{by: string, direction: string},
     *     filters: array{limit: int, search: string}
     * }
     */
    public function execute(array $filters = []): array
    {
        $sortBy = (string) ($filters['sort_by'] ?? 'name');
        $sortDir = (string) ($filters['sort_dir'] ?? 'asc');
        $limit = (int) ($filters['limit'] ?? 20);
        $search = trim((string) ($filters['search'] ?? ''));

        $paginator = $this->users->paginate($filters);

        $data = $paginator->getCollection()->map(
            fn (\App\Models\User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password_set' => $user->password !== null,
                'created_at' => $user->created_at?->toIso8601String(),
                'role_ids' => $user->roles->pluck('id')->values()->all(),
                'roles' => $user->roles->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                ])->values()->all(),
            ]
        )->values()->all();

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
            'sort' => [
                'by' => $sortBy,
                'direction' => $sortDir,
            ],
            'filters' => [
                'limit' => $limit,
                'search' => $search,
            ],
        ];
    }
}
