<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('number');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignUuid('season_id')->constrained('seasons')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journees');
    }
};
