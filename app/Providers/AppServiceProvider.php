<?php

namespace App\Providers;

use App\Contracts\Repositories\FicheRepositoryInterface;
use App\Contracts\Repositories\GameMatchRepositoryInterface;
use App\Contracts\Repositories\JourneeRepositoryInterface;
use App\Contracts\Repositories\LeagueRepositoryInterface;
use App\Contracts\Repositories\ParamDescriptionRepositoryInterface;
use App\Contracts\Repositories\RightRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\SeasonRepositoryInterface;
use App\Contracts\Repositories\SettingRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\TeamSummaryRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\EloquentFicheRepository;
use App\Repositories\EloquentGameMatchRepository;
use App\Repositories\EloquentJourneeRepository;
use App\Repositories\EloquentLeagueRepository;
use App\Repositories\EloquentParamDescriptionRepository;
use App\Repositories\EloquentRightRepository;
use App\Repositories\EloquentRoleRepository;
use App\Repositories\EloquentSeasonRepository;
use App\Repositories\EloquentSettingRepository;
use App\Repositories\EloquentTeamRepository;
use App\Repositories\EloquentTeamSummaryRepository;
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
        $this->app->bind(LeagueRepositoryInterface::class, EloquentLeagueRepository::class);
        $this->app->bind(SeasonRepositoryInterface::class, EloquentSeasonRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, EloquentTeamRepository::class);
        $this->app->bind(JourneeRepositoryInterface::class, EloquentJourneeRepository::class);
        $this->app->bind(GameMatchRepositoryInterface::class, EloquentGameMatchRepository::class);
        $this->app->bind(ParamDescriptionRepositoryInterface::class, EloquentParamDescriptionRepository::class);
        $this->app->bind(FicheRepositoryInterface::class, EloquentFicheRepository::class);
        $this->app->bind(TeamSummaryRepositoryInterface::class, EloquentTeamSummaryRepository::class);
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
