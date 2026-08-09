<?php

namespace App\UseCases\Journee;

use App\Contracts\Repositories\JourneeRepositoryInterface;
use App\Models\Journee;

class UpdateJourneeUseCase
{
    public function __construct(
        private readonly JourneeRepositoryInterface $journees,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(Journee $journee, array $data): Journee
    {
        return $this->journees->update($journee, $data);
    }
}
