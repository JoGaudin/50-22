<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $slugs = [
        'admin.leagues' => 'Ligues (admin)',
        'admin.seasons' => 'Saisons (admin)',
        'admin.teams' => 'Équipes (admin)',
        'admin.journees' => 'Journées (admin)',
        'admin.matches' => 'Matchs (admin)',
        'admin.param-descriptions' => 'Paramètres de fiche (admin)',
        'admin.fiches' => 'Fiches (admin)',
    ];

    public function up(): void
    {
        $now = now();
        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');

        foreach ($this->slugs as $slug => $name) {
            $rightId = (string) Str::uuid();

            DB::table('rights')->insert([
                'id' => $rightId,
                'name' => $name,
                'slug' => $slug,
                'description' => "Gérer la ressource « {$slug} » dans le back-office.",
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($adminRoleId) {
                DB::table('right_role')->insert([
                    'right_id' => $rightId,
                    'role_id' => $adminRoleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $slugs = array_keys($this->slugs);

        DB::table('right_role')
            ->whereIn('right_id', DB::table('rights')->whereIn('slug', $slugs)->pluck('id'))
            ->delete();

        DB::table('rights')->whereIn('slug', $slugs)->delete();
    }
};
