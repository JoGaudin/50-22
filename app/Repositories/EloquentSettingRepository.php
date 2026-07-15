<?php

namespace App\Repositories;

use App\Contracts\Repositories\SettingRepositoryInterface;
use App\Models\Setting;

class EloquentSettingRepository implements SettingRepositoryInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);
    }

    public function bool(string $key, bool $default = false): bool
    {
        return Setting::bool($key, $default);
    }

    public function all(): array
    {
        return Setting::query()
            ->get()
            ->pluck('value', 'key')
            ->all();
    }
}
