<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fiche extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'team_id',
        'created_by',
    ];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<ParamDescription, $this>
     */
    public function paramDescriptions(): BelongsToMany
    {
        return $this->belongsToMany(ParamDescription::class)
            ->using(FicheParamDescription::class)
            ->withPivot('description')
            ->orderBy('param_descriptions.order')
            ->orderBy('param_descriptions.name');
    }
}
