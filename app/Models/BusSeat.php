<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class BusSeat.
 *
 * @property int     $id
 * @property int     $bus_trip_id
 * @property int     $seat_number
 * @property bool    $is_reserved
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property BusTrip $busTrip
 *
 * @method static Builder|BusSeat newModelQuery()
 * @method static Builder|BusSeat newQuery()
 * @method static Builder|BusSeat query()
 * @method static Builder|BusSeat whereCreatedAt(Carbon $value)
 * @method static Builder|BusSeat whereId(int $value)
 * @method static Builder|BusSeat whereIsReserved(bool $value)
 * @method static Builder|BusSeat whereSeatNumber(int $value)
 * @method static Builder|BusSeat whereBusTripId(int $value)
 * @method static Builder|BusSeat whereUpdatedAt(Carbon $value)
 *
 * @mixin \Eloquent
 */
final class BusSeat extends Model
{
    use HasFactory;

    protected $table = 'bus_seats';

    protected $fillable = [
        'bus_trip_id',
        'seat_number',
        'is_reserved',
    ];

    /**
     * Get the bus trip associated with the bus seat.
     *
     * @return BelongsTo<BusTrip, BusSeat>
     */
    public function busTrip(): BelongsTo
    {
        return $this->belongsTo(BusTrip::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bus_trip_id' => 'integer',
            'seat_number' => 'integer',
            'is_reserved' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include available (not reserved) seats.
     *
     * @param Builder<BusSeat> $builder
     * @return Builder<BusSeat>
     */
    public function scopeAvailable(Builder $builder): Builder
    {
        return $builder->where('is_reserved', false);
    }

    /**
     * Scope a query to only include reserved seats.
     *
     * @param Builder<BusSeat> $builder
     * @return Builder<BusSeat>
     */
    public function scopeReserved(Builder $builder): Builder
    {
        return $builder->where('is_reserved', true);
    }

    /**
     * Scope a query to only include seats for a specific trip.
     *
     * @param Builder<BusSeat> $builder
     * @return Builder<BusSeat>
     */
    public function scopeForTrip(Builder $builder, int $tripId): Builder
    {
        return $builder->where('bus_trip_id', $tripId);
    }

    /**
     * Scope a query to exclude seats with specific seat numbers.
     *
     * @param Builder<BusSeat> $builder
     * @param array<int> $seatNumbers
     * @return Builder<BusSeat>
     */
    public function scopeNotInSeatNumbers(Builder $builder, array $seatNumbers): Builder
    {
        return $builder->whereNotIn('seat_number', $seatNumbers);
    }
}
