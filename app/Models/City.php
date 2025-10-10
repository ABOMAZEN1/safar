<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class City.
 *
 * @property int                      $id
 * @property string                   $name_en
 * @property string                   $name_ar
 * @property int                      $population
 * @property bool                     $is_governorate_center
 * @property Carbon|null              $created_at
 * @property Carbon|null              $updated_at
 * @property Collection<int, Bus>     $buses
 * @property int|null                 $buses_count
 * @property string                   $name
 * @property Collection<int, BusTrip> $arrivalTrips
 * @property int|null                 $arrival_trips_count
 * @property Collection<int, BusTrip> $departureTrips
 * @property int|null                 $departure_trips_count
 *
 * @method static Builder|City newModelQuery()
 * @method static Builder|City newQuery()
 * @method static Builder|City query()
 * @method static Builder|City whereCreatedAt(Carbon $value)
 * @method static Builder|City whereId(int $value)
 * @method static Builder|City whereName(string $value)
 * @method static Builder|City whereUpdatedAt(Carbon $value)
 *
 * @mixin \Eloquent
 */
final class City extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'population',
    ];

    /**
     * Get the buses associated with the city.
     *
     * @return HasMany<Bus>
     */
    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }

    /**
     * Get the departure trips from this city.
     *
     * @return HasMany<BusTrip>
     */
    public function departureTrips(): HasMany
    {
        return $this->hasMany(BusTrip::class, 'from_city_id');
    }

    /**
     * Get the arrival trips to this city.
     *
     * @return HasMany<BusTrip>
     */
    public function arrivalTrips(): HasMany
    {
        return $this->hasMany(BusTrip::class, 'to_city_id');
    }

    /**
     * Get the name based on the current locale.
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'population' => 'integer',
            'is_governorate_center' => 'boolean',
        ];
    }
}
