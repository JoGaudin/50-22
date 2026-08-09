<?php

namespace App\UseCases\ParamDescription;

use App\Contracts\Repositories\ParamDescriptionRepositoryInterface;
use App\Models\ParamDescription;

class DeleteParamDescriptionUseCase
{
    public function __construct(
        private readonly ParamDescriptionRepositoryInterface $paramDescriptions,
    ) {}

    public function execute(ParamDescription $paramDescription): void
    {
        $this->paramDescriptions->delete($paramDescription);
    }
}
