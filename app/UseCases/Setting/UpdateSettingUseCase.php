<?php

namespace App\UseCases\Setting;

use App\Contracts\Repositories\SettingRepositoryInterface;

class UpdateSettingUseCase
{
    public function __construct(
        private readonly SettingRepositoryInterface $settings,
    ) {}

    public function execute(string $key, mixed $value): void
    {
        $this->settings->set($key, $value);
    }

    /**
     * @param array<string, mixed> $values
     */
    public function many(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->settings->set($key, $value);
        }
    }
}
