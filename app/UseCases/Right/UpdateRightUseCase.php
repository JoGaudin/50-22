<?php

namespace App\UseCases\Right;

use App\Contracts\Repositories\RightRepositoryInterface;
use App\Models\Right;
use RuntimeException;

class UpdateRightUseCase
{
    public function __construct(
        private readonly RightRepositoryInterface $rights,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @throws RuntimeException if the right slug is protected
     */
    public function execute(Right $right, array $data): Right
    {
        if ($right->slug === 'user' && isset($data['slug']) && $data['slug'] !== 'user') {
            throw new RuntimeException(__('Ce droit est protégé et ne peut pas être modifié.'));
        }

        return $this->rights->update($right, $data);
    }
}
