<?php

namespace App\Repositories;

use App\Contracts\Repositories\JourneeRepositoryInterface;
use App\Models\Journee;
use Illuminate\Database\Eloquent\Collection;

class EloquentJourneeRepository implements JourneeRepositoryInterface
{
    public function findById(string $id): ?Journee
    {
        return Journee::find($id);
    }

    public function all(): Collection
    {
        return Journee::query()->with('season:id,name')->orderBy('number')->get();
    }

    public function create(array $data): Journee
    {
        return Journee::create($data);
    }

    public function update(Journee $journee, array $data): Journee
    {
        $journee->update($data);

        return $journee->fresh() ?? $journee;
    }

    public function delete(Journee $journee): bool
    {
        return (bool) $journee->delete();
    }
}
