<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignUuid('home_fiche_id')->nullable()->after('outside_team_id')->constrained('fiches')->nullOnDelete();
            $table->foreignUuid('outside_fiche_id')->nullable()->after('home_fiche_id')->constrained('fiches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('home_fiche_id');
            $table->dropConstrainedForeignId('outside_fiche_id');
        });
    }
};
