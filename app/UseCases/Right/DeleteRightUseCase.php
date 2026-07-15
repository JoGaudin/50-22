<?php

namespace App\UseCases\Right;

use App\Contracts\Repositories\RightRepositoryInterface;
use App\Models\Right;
use RuntimeException;

class DeleteRightUseCase
{
    public function __construct(
        private readonly RightRepositoryInterface $rights,
    ) {}

    /**
     * @throws RuntimeException if the right is protected
     */
    public function execute(Right $right): void
    {
        if ($right->slug === 'user') {
            throw new RuntimeException(__('Ce droit est protégé et ne peut pas être supprimé.'));
        }

        $this->rights->delete($right);
    }
}
