<?php

namespace App\UseCases\ParamDescription;

use App\Contracts\Repositories\ParamDescriptionRepositoryInterface;
use App\Models\ParamDescription;

class CreateParamDescriptionUseCase
{
    public function __construct(
        private readonly ParamDescriptionRepositoryInterface $paramDescriptions,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): ParamDescription
    {
        return $this->paramDescriptions->create($data);
    }
}
