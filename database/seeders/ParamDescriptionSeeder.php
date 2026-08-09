<?php

namespace Database\Seeders;

use App\Models\ParamDescription;
use Illuminate\Database\Seeder;

class ParamDescriptionSeeder extends Seeder
{
    private array $params = [
        'Touche OFF (construction maul, long transfert, seconde structure)',
        'Touche DEF (défense maul, jumping, prise en l\'air)',
        'Mêlée',
        'Comportement def proche ligne de but',
        'Comportement moments clés',
        'Jeu au sol OFF',
        'Jeu au sol DEF',
        'Espace',
        'JD',
    ];

    public function run(): void
    {
        foreach ($this->params as $index => $name) {
            ParamDescription::query()->firstOrCreate(['name' => $name], ['order' => $index]);
        }
    }
}
