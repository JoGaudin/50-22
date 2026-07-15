<?php

namespace App\Contracts\Repositories;

interface SettingRepositoryInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    public function bool(string $key, bool $default = false): bool;

    /**
     * @return array<string, mixed>
     */
    public function all(): array;
}
