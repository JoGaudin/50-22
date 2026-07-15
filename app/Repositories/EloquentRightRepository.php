<?php

namespace App\Repositories;

use App\Contracts\Repositories\RightRepositoryInterface;
use App\Models\Right;
use Illuminate\Database\Eloquent\Collection;

class EloquentRightRepository implements RightRepositoryInterface
{
    public function findById(string $id): ?Right
    {
        return Right::find($id);
    }

    public function findBySlug(string $slug): ?Right
    {
        return Right::query()->where('slug', $slug)->first();
    }

    public function all(): Collection
    {
        return Right::query()->orderBy('slug')->get();
    }

    public function create(array $data): Right
    {
        return Right::create($data);
    }

    public function update(Right $right, array $data): Right
    {
        $right->update($data);

        return $right->fresh() ?? $right;
    }

    public function delete(Right $right): bool
    {
        return (bool) $right->delete();
    }
}
