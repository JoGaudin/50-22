<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FicheParamDescription extends Pivot
{
    use HasUuids;

    protected $table = 'fiche_param_description';

    public $incrementing = false;

    protected $keyType = 'string';
}
