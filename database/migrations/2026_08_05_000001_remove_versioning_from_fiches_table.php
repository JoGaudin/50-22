<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiches', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'version_number']);
            $table->dropColumn('version_number');
        });
    }

    public function down(): void
    {
        Schema::table('fiches', function (Blueprint $table) {
            $table->unsignedInteger('version_number')->default(1)->after('name');
        });

        // Un simple défaut de 1 entrerait en conflit avec la contrainte unique dès qu'une équipe a
        // plusieurs fiches : on recalcule un rang par équipe, ordonné par date de création.
        DB::statement(<<<'SQL'
            update fiches f
            set version_number = sub.rn
            from (
                select id, row_number() over (partition by team_id order by created_at) as rn
                from fiches
            ) sub
            where f.id = sub.id
        SQL);

        Schema::table('fiches', function (Blueprint $table) {
            $table->unique(['team_id', 'version_number']);
        });
    }
};
