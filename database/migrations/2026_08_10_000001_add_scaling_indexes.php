<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->index('league_id');
            $table->unique(['league_id', 'name']);
        });

        Schema::table('journees', function (Blueprint $table) {
            $table->index('season_id');
            $table->unique(['season_id', 'number']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->index('journee_id');
            $table->index('home_team_id');
            $table->index('outside_team_id');
            $table->index('referee_id');
        });

        Schema::table('season_team', function (Blueprint $table) {
            $table->index('team_id');
        });

        Schema::table('league_user', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropUnique(['league_id', 'name']);
            $table->dropIndex(['league_id']);
        });

        Schema::table('journees', function (Blueprint $table) {
            $table->dropUnique(['season_id', 'number']);
            $table->dropIndex(['season_id']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['journee_id']);
            $table->dropIndex(['home_team_id']);
            $table->dropIndex(['outside_team_id']);
            $table->dropIndex(['referee_id']);
        });

        Schema::table('season_team', function (Blueprint $table) {
            $table->dropIndex(['team_id']);
        });

        Schema::table('league_user', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
