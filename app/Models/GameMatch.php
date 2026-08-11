<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMatch extends Model
{
    use HasUuids;

    protected $table = 'matches';

    protected $fillable = [
        'date',
        'journee_id',
        'home_team_id',
        'outside_team_id',
        'home_team_score',
        'outside_team_score',
        'referee_id',
        'referee_name',
        'home_fiche_id',
        'outside_fiche_id',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'home_team_score' => 'integer',
            'outside_team_score' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Journee, $this>
     */
    public function journee(): BelongsTo
    {
        return $this->belongsTo(Journee::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function outsideTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'outside_team_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    /**
     * @return BelongsTo<Fiche, $this>
     */
    public function homeFiche(): BelongsTo
    {
        return $this->belongsTo(Fiche::class, 'home_fiche_id');
    }

    /**
     * @return BelongsTo<Fiche, $this>
     */
    public function outsideFiche(): BelongsTo
    {
        return $this->belongsTo(Fiche::class, 'outside_fiche_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
