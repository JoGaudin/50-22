<?php

namespace App\Providers;

use App\Contracts\Repositories\RightRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\SettingRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\EloquentRightRepository;
use App\Repositories\EloquentRoleRepository;
use App\Repositories\EloquentSettingRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, EloquentRoleRepository::class);
        $this->app->bind(RightRepositoryInterface::class, EloquentRightRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, EloquentSettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if ($root = config('app.url')) {
            URL::forceRootUrl($root);
        }
    }
}
