<?php

namespace App\UseCases\ParamDescription;

use App\Contracts\Repositories\ParamDescriptionRepositoryInterface;
use App\Models\ParamDescription;

class UpdateParamDescriptionUseCase
{
    public function __construct(
        private readonly ParamDescriptionRepositoryInterface $paramDescriptions,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(ParamDescription $paramDescription, array $data): ParamDescription
    {
        return $this->paramDescriptions->update($paramDescription, $data);
    }
}
