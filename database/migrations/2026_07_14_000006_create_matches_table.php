<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->foreignUuid('journee_id')->constrained('journees')->restrictOnDelete();
            $table->foreignUuid('home_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignUuid('outside_team_id')->constrained('teams')->restrictOnDelete();
            $table->integer('home_team_score')->nullable();
            $table->integer('outside_team_score')->nullable();
            $table->string('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
