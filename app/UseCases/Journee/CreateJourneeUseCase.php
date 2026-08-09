<?php

namespace App\UseCases\Journee;

use App\Contracts\Repositories\JourneeRepositoryInterface;
use App\Models\Journee;

class CreateJourneeUseCase
{
    public function __construct(
        private readonly JourneeRepositoryInterface $journees,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): Journee
    {
        return $this->journees->create($data);
    }
}
