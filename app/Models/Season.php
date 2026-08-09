<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Season extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'start',
        'end',
        'league_id',
    ];

    /**
     * @return BelongsTo<League, $this>
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * @return BelongsToMany<Team, $this>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * @return HasMany<Journee, $this>
     */
    public function journees(): HasMany
    {
        return $this->hasMany(Journee::class);
    }

    /**
     * @return HasManyThrough<GameMatch, Journee, $this>
     */
    public function matches(): HasManyThrough
    {
        return $this->hasManyThrough(GameMatch::class, Journee::class);
    }
}
