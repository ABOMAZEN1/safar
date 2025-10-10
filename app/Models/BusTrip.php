<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class BusTrip.
 *
 * Represents a bus trip between two cities.
 *
 * @property int $id
 * @property int $from_city_id
 * @property int $to_city_id
 * @property int $bus_id
 * @property int|null $bus_driver_id
 * @property int $travel_company_id
 * @property Carbon $departure_datetime
 * @property Carbon|null $return_datetime
 * @property string $duration_of_departure_trip
 * @property string|null $duration_of_return_trip
 * @property string $trip_type
 * @property int $number_of_seats
 * @property int $remaining_seats
 * @property string $ticket_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property TravelCompany|null $travelCompany
 * @property Bus|null $bus
 * @property BusDriver|null $busDriver
 * @property City|null $fromCity
 * @property City|null $toCity
 *
 * @method static Builder|BusTrip availableTrips()
 * @method static Builder|BusTrip newModelQuery()
 * @method static Builder|BusTrip newQuery()
 * @method static Builder|BusTrip query()
 *
 * @property Collection|BusTripBooking[] $bookings
 * @property int|null $bookings_count
 *
 * @mixin \Eloquent
 */
final class BusTrip extends Model
{
    protected $fillable = [
        'from_city_id',
        'to_city_id',
        'bus_id',
        'bus_driver_id',
        'travel_company_id',
        'departure_datetime',
        'return_datetime',
        'duration_of_departure_trip',
        'duration_of_return_trip',
        'trip_type',
        'number_of_seats',
        'remaining_seats',
        'ticket_price',
    ];

    /**
     * Scope to filter trips by available seats.
     */
    public function scopeAvailableTrips(Builder $builder): Builder
    {
        return $builder->where('remaining_seats', '>', 0);
    }

    /**
     * Book seats for this trip.
     *
     * @throws Exception
     */
    public function bookSeats(int $numberOfSeats = 1): void
    {
        if ($this->remaining_seats < $numberOfSeats) {
            throw new Exception('Not enough seats available.');
        }

        $this->decrement('remaining_seats', $numberOfSeats);
    }

    /**
     * Cancel booked seats for this trip.
     *
     * @throws Exception
     */
    public function cancelSeats(int $count = 1): void
    {
        if (($this->remaining_seats + $count) > $this->number_of_seats) {
            throw new Exception('Cannot exceed total number of seats.');
        }

        $this->increment('remaining_seats', $count);
    }

    /**
     * Get the driver associated with the bus trip.
     */
    public function busDriver(): BelongsTo
    {
        return $this->belongsTo(BusDriver::class);
    }

    /**
     * Get the bus associated with the bus trip.
     */
    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Get the company associated with the bus trip.
     */
    public function travelCompany(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class);
    }

    /**
     * Get the city from which the trip departs.
     */
    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * Get the city to which the trip arrives.
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * Get the bookings associated with the bus trip.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(BusTripBooking::class, 'bus_trip_id');
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'ticket_price' => 'decimal:2',
            'remaining_seats' => 'integer',
            'departure_datetime' => 'datetime',
            'return_datetime' => 'datetime',
        ];
    }

    /**
     * Get the trip title attribute.
     */
    public function getTripsAttribute(): string
    {
        $fromCity = $this->fromCity?->name ?? 'Unknown';
        $toCity = $this->toCity?->name ?? 'Unknown';
        return "{$fromCity} → {$toCity}";
    }
}
