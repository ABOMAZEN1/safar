<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class TravelCompanion.
 *
 * @property int $id The unique identifier for the travel companion.
 * @property int $bus_trip_booking_id The identifier for the associated booking.
 * @property string $companion_name The name of the travel companion.
 * @property Carbon|null $created_at The timestamp when the travel companion was created.
 * @property Carbon|null $updated_at The timestamp when the travel companion was last updated.
 *
 * @method static Builder|TravelCompanion newModelQuery()
 * @method static Builder|TravelCompanion newQuery()
 * @method static Builder|TravelCompanion query()
 * @method static Builder|TravelCompanion whereBookingId(int $value)
 * @method static Builder|TravelCompanion whereCreatedAt(Carbon|null $value)
 * @method static Builder|TravelCompanion whereId(int $value)
 * @method static Builder|TravelCompanion whereCompanionName(string $value)
 * @method static Builder|TravelCompanion whereUpdatedAt(Carbon|null $value)
 *
 * @property-read BusTripBooking|null $busTripBooking
 *
 * @mixin \Eloquent
 */
final class TravelCompanion extends Model
{
    use HasFactory;

    protected $table = 'travel_companions';

    protected $fillable = [
        'bus_trip_booking_id',
        'companion_name',
    ];

    /**
     * Get the booking associated with the travel companion.
     *
     * @return BelongsTo<BusTripBooking, TravelCompanion>
     */
    public function busTripBooking(): BelongsTo
    {
        return $this->belongsTo(BusTripBooking::class);
    }

    /**
     * Get the casting for the model attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bus_trip_booking_id' => 'integer',
            'companion_name' => 'string',
        ];
    }
}
