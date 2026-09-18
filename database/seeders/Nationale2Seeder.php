<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Nationale2Seeder extends Seeder
{
    /**
     * Clubs de Nationale 2 pour la saison 2026-2027 (poules 1 et 2 FFR confondues,
     * la table `teams` ne modélise pas la notion de poule).
     *
     * @see https://www.lerugbynistere.fr/news/nationale-et-nationale-2-la-ffr-devoile-les-poules-pour-la-saison-2026-2027/
     */
    private array $teams = [
        ['name' => 'AS Mâconnaise', 'ville' => 'Mâcon', 'stade' => 'Stade Émile Vanier'],
        ['name' => 'CS Annonay', 'ville' => 'Annonay', 'stade' => 'Stade Antonio Pinto'],
        ['name' => 'Club Municipal de Floirac', 'ville' => 'Floirac', 'stade' => 'Stade Jean-Raymond Guyon'],
        ['name' => 'RC Savoie Rumilly', 'ville' => 'Rumilly', 'stade' => 'Stade des Grangettes Jean Dunand'],
        ['name' => 'Union Drancy Saint-Denis Rugby 93', 'ville' => 'Drancy', 'stade' => 'Stade Guy-Môquet'],
        ['name' => 'Saint-Médard Rugby Club', 'ville' => 'Saint-Médard-en-Jalles', 'stade' => 'Complexe Robert Monc'],
        ['name' => 'Servette Rugby Club de Genève', 'ville' => 'Genève (Suisse)', 'stade' => 'Stade de la Plaine des Sports (Valserhône)'],
        ['name' => 'Stade Langonnais', 'ville' => 'Langon', 'stade' => 'Stade Comberlin'],
        ['name' => 'Stade Métropolitain', 'ville' => 'Villeurbanne', 'stade' => 'Stade Boiron Granger'],
        ['name' => 'Stade Nantais', 'ville' => 'Nantes', 'stade' => 'Stade Pascal-Laporte'],
        ['name' => 'Union Sportive Marmandaise', 'ville' => 'Marmande', 'stade' => 'Stade Georges Dartiailh'],
        ['name' => 'Union Sportive de Salles', 'ville' => 'Salles', 'stade' => 'Stade Raymond Brun'],
        ['name' => 'AS Fleurance', 'ville' => 'Fleurance', 'stade' => 'Stade Marius Lacoste'],
        ['name' => 'Avenir Valencien', 'ville' => "Valence-d'Agen", 'stade' => 'Stade Évelyne-Jean-Baylet'],
        ['name' => 'Rugby Club Aubenas Vals', 'ville' => 'Aubenas', 'stade' => 'Stade Georges Marquand'],
        ['name' => 'Rugby Club Auch', 'ville' => 'Auch', 'stade' => 'Stade Jacques-Fouroux'],
        ['name' => 'Rugby Club Nîmois', 'ville' => 'Nîmes', 'stade' => 'Stade Nicolas Kaufmann'],
        ['name' => 'Rugby Club Tricastin', 'ville' => 'Saint-Paul-Trois-Châteaux', 'stade' => 'Stade Georges Perriod'],
        ['name' => 'Sport Athlétique Mauléonais', 'ville' => 'Mauléon', 'stade' => 'Stade Marius-Rodrigo'],
        ['name' => 'Saint-Jean-de-Luz Olympique Rugby', 'ville' => 'Saint-Jean-de-Luz', 'stade' => 'Stade du Pavillon Bleu'],
        ['name' => 'Sporting Club Graulhetois', 'ville' => 'Graulhet', 'stade' => 'Stade Noël Pélissou'],
        ['name' => "Peyrehorade Sports Rugby Pays d'Orthe et Arrigans", 'ville' => 'Peyrehorade', 'stade' => 'Stade Joseph Dabadie'],
        ['name' => 'Union Sportive Seynoise', 'ville' => 'La Seyne-sur-Mer', 'stade' => 'Stade Victor-Marquet'],
        ['name' => 'Union Sportive Tyrosse Rugby Côte Sud', 'ville' => 'Saint-Vincent-de-Tyrosse', 'stade' => 'Stade La Fougère'],
    ];

    public function run(): void
    {
        $league = League::query()->firstOrCreate(['name' => 'Nationale 2']);

        $season = Season::query()->firstOrCreate(
            ['name' => '2026-2027', 'league_id' => $league->id],
            ['start' => '2026-09-06', 'end' => '2027-05-30'],
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
