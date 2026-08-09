<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiches', function (Blueprint $table) {
            $table->timestamps();
            $table->unsignedInteger('version_number')->default(1)->after('name');
            $table->unique(['team_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::table('fiches', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'version_number']);
            $table->dropColumn(['version_number', 'created_at', 'updated_at']);
        });
    }
};
