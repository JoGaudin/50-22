<?php

namespace App\Repositories;

use App\Contracts\Repositories\ParamDescriptionRepositoryInterface;
use App\Models\ParamDescription;
use Illuminate\Database\Eloquent\Collection;

class EloquentParamDescriptionRepository implements ParamDescriptionRepositoryInterface
{
    public function findById(string $id): ?ParamDescription
    {
        return ParamDescription::find($id);
    }

    public function all(): Collection
    {
        return ParamDescription::query()->orderBy('order')->orderBy('name')->get();
    }

    public function create(array $data): ParamDescription
    {
        return ParamDescription::create($data);
    }

    public function update(ParamDescription $paramDescription, array $data): ParamDescription
    {
        $paramDescription->update($data);

        return $paramDescription->fresh() ?? $paramDescription;
    }

    public function delete(ParamDescription $paramDescription): bool
    {
        return (bool) $paramDescription->delete();
    }
}
