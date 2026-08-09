<?php

namespace App\UseCases\ParamDescription;

use App\Contracts\Repositories\ParamDescriptionRepositoryInterface;
use App\Models\ParamDescription;
use Illuminate\Database\Eloquent\Collection;

class ListParamDescriptionsUseCase
{
    public function __construct(
        private readonly ParamDescriptionRepositoryInterface $paramDescriptions,
    ) {}

    /**
     * @return Collection<int, ParamDescription>
     */
    public function execute(): Collection
    {
        return $this->paramDescriptions->all();
    }
}
