<?php

namespace App\Contracts\Repositories;

use App\Models\ParamDescription;
use Illuminate\Database\Eloquent\Collection;

interface ParamDescriptionRepositoryInterface
{
    public function findById(string $id): ?ParamDescription;

    /**
     * @return Collection<int, ParamDescription>
     */
    public function all(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): ParamDescription;

    /**
     * @param array<string, mixed> $data
     */
    public function update(ParamDescription $paramDescription, array $data): ParamDescription;

    public function delete(ParamDescription $paramDescription): bool;
}
