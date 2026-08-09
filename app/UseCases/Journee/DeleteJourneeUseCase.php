<?php

namespace App\UseCases\Journee;

use App\Contracts\Repositories\JourneeRepositoryInterface;
use App\Models\Journee;
use RuntimeException;

class DeleteJourneeUseCase
{
    public function __construct(
        private readonly JourneeRepositoryInterface $journees,
    ) {}

    /**
     * @throws RuntimeException if the journee still has matches
     */
    public function execute(Journee $journee): void
    {
        if ($journee->matches()->exists()) {
            throw new RuntimeException(__('Cette journée a des matchs associés et ne peut pas être supprimée.'));
        }

        $this->journees->delete($journee);
    }
}
