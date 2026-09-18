<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class League extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
    ];

    protected $appends = [
        'logo_url',
    ];

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->logo ? Storage::disk(config()->string('filesystems.default'))->url($this->logo) : null,
        );
    }

    /**
     * @return HasMany<Season, $this>
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function referees(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function currentSeason(): ?Season
    {
        $today = Carbon::today()->toDateString();

        return $this->seasons()
            ->where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->first()
            ?? $this->seasons()->orderByDesc('start')->first();
    }
}
