<?php

namespace App\UseCases\Setting;

use App\Contracts\Repositories\SettingRepositoryInterface;

class GetSettingUseCase
{
    public function __construct(
        private readonly SettingRepositoryInterface $settings,
    ) {}

    public function execute(string $key, mixed $default = null): mixed
    {
        return $this->settings->get($key, $default);
    }

    public function bool(string $key, bool $default = false): bool
    {
        return $this->settings->bool($key, $default);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->settings->all();
    }
}
