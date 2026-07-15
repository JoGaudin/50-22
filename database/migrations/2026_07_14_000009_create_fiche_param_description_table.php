<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiche_param_description', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fiche_id')->constrained('fiches')->cascadeOnDelete();
            $table->foreignUuid('param_description_id')->constrained('param_descriptions')->restrictOnDelete();
            $table->text('description');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiche_param_description');
    }
};
