<?php

namespace App\UseCases\Right;

use App\Contracts\Repositories\RightRepositoryInterface;
use App\Models\Right;

class CreateRightUseCase
{
    public function __construct(
        private readonly RightRepositoryInterface $rights,
    ) {}

    public function execute(string $name, string $slug, ?string $description = null): Right
    {
        return $this->rights->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);
    }
}
