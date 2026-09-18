<?php

use App\Http\Controllers\Admin\FicheManagementController;
use App\Http\Controllers\Admin\GameMatchManagementController;
use App\Http\Controllers\Admin\JourneeManagementController;
use App\Http\Controllers\Admin\LeagueManagementController;
use App\Http\Controllers\Admin\ParamDescriptionManagementController;
use App\Http\Controllers\Admin\RightManagementController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\SeasonManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TeamManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\Referee\FicheCompareController;
use App\Http\Controllers\Referee\FicheController as RefereeFicheController;
use App\Http\Controllers\Referee\LeagueController as RefereeLeagueController;
use App\Http\Controllers\Referee\MatchController as RefereeMatchController;
use App\Http\Controllers\Referee\TeamPanelController;
use App\Http\Controllers\Referee\TeamSummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('right:admin.users')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/invite', [UserManagementController::class, 'invite'])->name('users.invite');
        Route::patch('/users/{user}/roles', [UserManagementController::class, 'syncRoles'])->name('users.roles.sync');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('right:admin.roles')->group(function () {
        Route::get('/roles', [RoleManagementController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleManagementController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleManagementController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleManagementController::class, 'destroy'])->name('roles.destroy');
    });

    Route::middleware('right:admin.rights')->group(function () {
        Route::get('/rights', [RightManagementController::class, 'index'])->name('rights.index');
        Route::post('/rights', [RightManagementController::class, 'store'])->name('rights.store');
        Route::put('/rights/{right}', [RightManagementController::class, 'update'])->name('rights.update');
        Route::delete('/rights/{right}', [RightManagementController::class, 'destroy'])->name('rights.destroy');
    });

    Route::middleware('right:admin.settings')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('right:admin.leagues')->group(function () {
        Route::get('/leagues', [LeagueManagementController::class, 'index'])->name('leagues.index');
        Route::post('/leagues', [LeagueManagementController::class, 'store'])->name('leagues.store');
        Route::put('/leagues/{league}', [LeagueManagementController::class, 'update'])->name('leagues.update');
        Route::delete('/leagues/{league}', [LeagueManagementController::class, 'destroy'])->name('leagues.destroy');
    });

    Route::middleware('right:admin.seasons')->group(function () {
        Route::get('/seasons', [SeasonManagementController::class, 'index'])->name('seasons.index');
        Route::post('/seasons', [SeasonManagementController::class, 'store'])->name('seasons.store');
        Route::put('/seasons/{season}', [SeasonManagementController::class, 'update'])->name('seasons.update');
        Route::delete('/seasons/{season}', [SeasonManagementController::class, 'destroy'])->name('seasons.destroy');
    });

    Route::middleware('right:admin.teams')->group(function () {
        Route::get('/teams', [TeamManagementController::class, 'index'])->name('teams.index');
        Route::post('/teams', [TeamManagementController::class, 'store'])->name('teams.store');
        Route::put('/teams/{team}', [TeamManagementController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{team}', [TeamManagementController::class, 'destroy'])->name('teams.destroy');
    });

    Route::middleware('right:admin.journees')->group(function () {
        Route::get('/journees', [JourneeManagementController::class, 'index'])->name('journees.index');
        Route::post('/journees', [JourneeManagementController::class, 'store'])->name('journees.store');
        Route::put('/journees/{journee}', [JourneeManagementController::class, 'update'])->name('journees.update');
        Route::delete('/journees/{journee}', [JourneeManagementController::class, 'destroy'])->name('journees.destroy');
    });

    Route::middleware('right:admin.matches')->group(function () {
        Route::get('/matches', [GameMatchManagementController::class, 'index'])->name('matches.index');
        Route::post('/matches', [GameMatchManagementController::class, 'store'])->name('matches.store');
        Route::put('/matches/{match}', [GameMatchManagementController::class, 'update'])->name('matches.update');
        Route::delete('/matches/{match}', [GameMatchManagementController::class, 'destroy'])->name('matches.destroy');
    });

    Route::middleware('right:admin.param-descriptions')->group(function () {
        Route::get('/param-descriptions', [ParamDescriptionManagementController::class, 'index'])->name('param-descriptions.index');
        Route::post('/param-descriptions', [ParamDescriptionManagementController::class, 'store'])->name('param-descriptions.store');
        Route::put('/param-descriptions/{paramDescription}', [ParamDescriptionManagementController::class, 'update'])->name('param-descriptions.update');
        Route::delete('/param-descriptions/{paramDescription}', [ParamDescriptionManagementController::class, 'destroy'])->name('param-descriptions.destroy');
    });

    Route::middleware('right:admin.fiches')->group(function () {
        Route::get('/fiches', [FicheManagementController::class, 'index'])->name('fiches.index');
        Route::post('/fiches', [FicheManagementController::class, 'store'])->name('fiches.store');
        Route::put('/fiches/{fiche}', [FicheManagementController::class, 'update'])->name('fiches.update');
        Route::delete('/fiches/{fiche}', [FicheManagementController::class, 'destroy'])->name('fiches.destroy');
    });
});

Route::middleware(['auth', 'verified'])->prefix('referee')->name('referee.')->group(function () {
    Route::get('/', [RefereeLeagueController::class, 'index'])->name('leagues.index');
    Route::get('/leagues/{league}', [RefereeLeagueController::class, 'show'])->name('leagues.show');
    Route::post('/leagues/{league}/join', [RefereeLeagueController::class, 'join'])->name('leagues.join');
    Route::delete('/leagues/{league}/leave', [RefereeLeagueController::class, 'leave'])->name('leagues.leave');
    Route::post('/leagues/{league}/matches', [RefereeMatchController::class, 'store'])->name('leagues.matches.store');
    Route::get('/matches', [RefereeMatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/{match}', [RefereeMatchController::class, 'show'])->name('matches.show');
    Route::patch('/matches/{match}', [RefereeMatchController::class, 'update'])->name('matches.update');
    Route::patch('/matches/{match}/claim', [RefereeMatchController::class, 'claim'])->name('matches.claim');
    Route::post('/matches/{match}/fiches', [RefereeMatchController::class, 'createFiche'])->name('matches.fiches.store');
    Route::get('/matches/{match}/fiches/pdf', [RefereeMatchController::class, 'exportFiches'])->name('matches.fiches.pdf');
    Route::get('/teams/{team}/fiches', [RefereeFicheController::class, 'index'])->name('teams.fiches.index');
    Route::post('/teams/{team}/fiches', [RefereeFicheController::class, 'store'])->name('teams.fiches.store');
    Route::get('/teams/{team}/panel', [TeamPanelController::class, 'show'])->name('teams.panel');
    Route::post('/teams/{team}/summary', [TeamSummaryController::class, 'store'])->name('teams.summary.store');
    Route::get('/teams/{team}/param-descriptions/{paramDescription}/answers', [FicheCompareController::class, 'answers'])->name('teams.param-answers');
    Route::get('/fiches/compare', [FicheCompareController::class, 'index'])->name('fiches.compare');
    Route::put('/fiches/{fiche}', [RefereeFicheController::class, 'update'])->name('fiches.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/user/push-subscription', [PushSubscriptionController::class, 'store'])->name('push-subscription.store');
    Route::delete('/user/push-subscription', [PushSubscriptionController::class, 'destroy'])->name('push-subscription.destroy');
});

require __DIR__.'/auth.php';
