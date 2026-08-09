<?php

namespace Database\Seeders;

use App\Models\Journee;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RugbyNationaleSeeder extends Seeder
{
    /**
     * Poule unique de 14 équipes en aller-retour : 13 journées aller + 13 journées retour.
     */
    private const JOURNEES_PAR_SAISON = 26;

    /**
     * Équipes de Nationale (3e division) pour la saison 2026-2027.
     *
     * @see https://fr.wikipedia.org/wiki/Championnat_de_France_de_rugby_%C3%A0_XV_de_Nationale_2026-2027
     */
    private array $teams = [
        ['name' => 'SC Albi', 'ville' => 'Albi', 'stade' => 'Stadium municipal'],
        ['name' => 'CS Bourgoin-Jallieu', 'ville' => 'Bourgoin-Jallieu', 'stade' => 'Stade Pierre-Rajon'],
        ['name' => 'US bressane', 'ville' => 'Bourg-en-Bresse', 'stade' => 'Stade Marcel-Verchère'],
        ['name' => 'US Carcassonne', 'ville' => 'Carcassonne', 'stade' => 'Stade Albert-Domec'],
        ['name' => 'SO Chambéry', 'ville' => 'Chambéry', 'stade' => 'Chambéry Savoie Stadium'],
        ['name' => 'Olympique marcquois', 'ville' => 'Marcq-en-Barœul', 'stade' => 'Stadium Lille Métropole'],
        ['name' => 'RC Massy', 'ville' => 'Massy', 'stade' => 'Stade Jules-Ladoumègue'],
        ['name' => 'Stade montois', 'ville' => 'Mont-de-Marsan', 'stade' => 'Stade André-et-Guy-Boniface'],
        ['name' => 'RC Orléans', 'ville' => 'Orléans', 'stade' => 'Stade Marcel-Garcin'],
        ['name' => 'CA Périgueux', 'ville' => 'Périgueux', 'stade' => 'Stade Francis-Rongiéras'],
        ['name' => 'Rennes EC', 'ville' => 'Rennes', 'stade' => 'Stade du Commandant Bougouin'],
        ['name' => 'Rouen NR', 'ville' => 'Rouen', 'stade' => 'Stade Robert-Diochon'],
        ['name' => 'RC Suresnes', 'ville' => 'Suresnes', 'stade' => 'Stade Jean-Moulin'],
        ['name' => 'CS Vienne', 'ville' => 'Vienne', 'stade' => 'Stade Jean-Etcheberry'],
    ];

    public function run(): void
    {
        $league = League::query()->firstOrCreate(['name' => 'Nationale 1']);

        $season = Season::query()->firstOrCreate(
            ['name' => '2026-2027', 'league_id' => $league->id],
            ['start' => '2026-08-23', 'end' => '2027-05-16'],
        );

        $teams = collect($this->teams)->map(
            fn (array $row) => Team::query()->updateOrCreate(
                ['slug' => Str::slug($row['name'])],
                $row,
            )
        );

        $season->teams()->syncWithoutDetaching($teams->pluck('id'));

        $referee = User::query()->updateOrCreate(
            ['email' => 'arbitre@example.com'],
            [
                'name' => 'Arbitre Démo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $league->referees()->syncWithoutDetaching([$referee->id]);

        $journeeStart = Carbon::parse($season->start);
        collect(range(1, self::JOURNEES_PAR_SAISON))->each(function (int $number) use ($season, $journeeStart) {
            $start = $journeeStart->copy()->addWeeks($number - 1);

            Journee::query()->firstOrCreate(
                ['season_id' => $season->id, 'number' => $number],
                ['start_date' => $start->toDateString(), 'end_date' => $start->copy()->addDays(2)->toDateString()],
            );
        });
    }
}
