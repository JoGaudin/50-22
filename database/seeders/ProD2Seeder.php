<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProD2Seeder extends Seeder
{
    /**
     * Clubs de PRO D2 pour la saison 2026-2027.
     *
     * @see https://fr.wikipedia.org/wiki/Championnat_de_France_de_rugby_%C3%A0_XV_de_2e_division_2026-2027
     */
    private array $teams = [
        ['name' => 'SU Agen', 'ville' => 'Agen', 'stade' => 'Stade Armandie'],
        ['name' => 'Stade aurillacois', 'ville' => 'Aurillac', 'stade' => 'Stade Jean-Alric'],
        ['name' => 'AS Béziers', 'ville' => 'Béziers', 'stade' => 'Stade Raoul-Barrière'],
        ['name' => 'Biarritz olympique', 'ville' => 'Biarritz', 'stade' => "Parc des sports d'Aguiléra"],
        ['name' => 'CA Brive', 'ville' => 'Brive-la-Gaillarde', 'stade' => 'Stade Amédée-Domenech'],
        ['name' => 'Colomiers Rugby', 'ville' => 'Colomiers', 'stade' => 'Stade Michel-Bendichou'],
        ['name' => 'US Dax', 'ville' => 'Dax', 'stade' => 'Stade Maurice-Boyau'],
        ['name' => 'FC Grenoble', 'ville' => 'Grenoble', 'stade' => 'Stade des Alpes'],
        ['name' => 'US Montauban', 'ville' => 'Montauban', 'stade' => 'Stade Sapiac'],
        ['name' => 'RC Narbonne', 'ville' => 'Narbonne', 'stade' => 'Parc des sports et de l\'amitié'],
        ['name' => 'USON Nevers', 'ville' => 'Sermoise-sur-Loire', 'stade' => 'Stade du Pré Fleuri'],
        ['name' => 'Nissa Rugby', 'ville' => 'Nice', 'stade' => 'Stade Marcel-Volot'],
        ['name' => 'Oyonnax Rugby', 'ville' => 'Oyonnax', 'stade' => 'Stade Charles-Mathon'],
        ['name' => 'Provence Rugby', 'ville' => 'Aix-en-Provence', 'stade' => 'Stade Maurice-David'],
        ['name' => 'Soyaux Angoulême XV', 'ville' => 'Angoulême', 'stade' => 'Stade Chanzy'],
        ['name' => 'Valence Romans DR', 'ville' => 'Valence', 'stade' => 'Stade Georges-Pompidou'],
    ];

    public function run(): void
    {
        $league = League::query()->firstOrCreate(['name' => 'PRO D2']);

        $season = Season::query()->firstOrCreate(
            ['name' => '2026-2027', 'league_id' => $league->id],
            ['start' => '2026-08-28', 'end' => '2027-06-04'],
        );

        $teams = collect($this->teams)->map(
            fn (array $row) => Team::query()->updateOrCreate(
                ['slug' => Str::slug($row['name'])],
                $row,
            )
        );

        $season->teams()->syncWithoutDetaching($teams->pluck('id'));
    }
}
