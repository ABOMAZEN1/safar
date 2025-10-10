<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\BookingStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * Class BusTripBooking.
 *
 * Represents a booking made by a customer for a specific bus trip.
 *
 * @property int           $id                     The unique identifier for the booking.
 * @property int           $customer_id            The ID of the customer who made the booking.
 * @property int           $bus_trip_id            The ID of the bus trip associated with the booking.
 * @property int           $reserved_seat_count    The number of seats reserved in the booking.
 * @property string        $qr_code_path           The QR code associated with the booking.
 * @property bool          $is_departure_confirmed Indicates if the departure has been confirmed.
 * @property bool          $is_return_confirmed    Indicates if the return has been confirmed.
 * @property string        $booking_status         The status of the booking.
 * @property string        $total_price            The total price of the booking.
 * @property string        $reserved_seat_numbers  The seat numbers reserved in the booking.
 * @property Carbon|null   $canceled_at            The timestamp when the booking was canceled.
 * @property Carbon|null   $created_at             The timestamp when the booking was created.
 * @property Carbon|null   $updated_at             The timestamp when the booking was last updated.
 * @property BusTrip|null  $busTrip                The bus trip associated with the booking.
 * @property Customer|null $customer               The customer who made the booking.
 *
 * @method static Builder|BusTripBooking newModelQuery()
 * @method static Builder|BusTripBooking newQuery()
 * @method static Builder|BusTripBooking query()
 * @method static Builder|BusTripBooking upcoming()
 * @method static Builder|BusTripBooking passed()
 * @method static Builder|BusTripBooking active()
 * @method static Builder|BusTripBooking forCustomer(int $customerId)
 * @method static Builder|BusTripBooking forTrip(int $tripId)
 * @method static Builder|BusTripBooking forCompany(int $companyId)
 * @method static Builder|BusTripBooking withStatus(string|array $status)
 * @method static Builder|BusTripBooking withoutStatus(string|array $status)
 * @method static Builder|BusTripBooking withReservedSeats()
 * @method static Builder|BusTripBooking canceled()
 * @method static Builder|BusTripBooking notCanceled()
 * @method static Builder|BusTripBooking paid()
 *
 * @property Collection|TravelCompanion[] $companions       The travel companions associated with the booking.
 * @property int|null                                         $companions_count The count of travel companions.
 *
 * @mixin \Eloquent
 */
final class BusTripBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'bus_trip_id',
        'reserved_seat_count',
        'qr_code_path',
        'is_departure_confirmed',
        'is_return_confirmed',
        'booking_status',
        'total_price',
        'reserved_seat_numbers',
        'canceled_at',
    ];

    /**
     * Get the customer who made the booking.
     *
     * @return BelongsTo<Customer, BusTripBooking>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the bus trip associated with the booking.
     *
     * @return BelongsTo<BusTrip, BusTripBooking>
     */
    public function busTrip(): BelongsTo
    {
        return $this->belongsTo(BusTrip::class, 'bus_trip_id');
    }

    /**
     * Get the companions associated with the booking.
     *
     * @return HasMany<TravelCompanion>
     */
    public function travelCompanions(): HasMany
    {
        return $this->hasMany(TravelCompanion::class, 'bus_booking_id');
    }

    /**
     * Cast attributes to native types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_departure_confirmed' => 'boolean',
            'is_return_confirmed' => 'boolean',
            'total_price' => 'decimal:2',
            'reserved_seat_count' => 'integer',
            'qr_code_path' => 'string',
            'canceled_at' => 'datetime',
        ];
    }

    /**
     * Accessor for the QR code path.
     *
     * @return Attribute<string, string>
     */
    public function qrCodePath(): Attribute
    {
        return Attribute::make(
            get: fn($value): string|UrlGenerator|null => $value ? url('storage/' . $value) : null,
            set: fn($value) => $value,
        );
    }

    /**
     * Scope a query to only include upcoming bookings.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeUpcoming(Builder $builder): Builder
    {
        return $builder->whereHas('busTrip', fn($q) => $q->where('departure_datetime', '>', now()))
            ->where('booking_status', '!=', BookingStatusEnum::CANCELED->value);
    }

    /**
     * Scope a query to only include passed bookings.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopePassed(Builder $builder): Builder
    {
        return $builder->where(function ($q): void {
            $q->whereHas('busTrip', fn($q) => $q->where('departure_datetime', '<', now()))
                ->orWhere('booking_status', BookingStatusEnum::CANCELED->value);
        });
    }

    /**
     * Scope a query to only include active (non-canceled, non-refunded) bookings.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereNotIn('booking_status', [
            BookingStatusEnum::CANCELED->value,
            BookingStatusEnum::REFUNDED->value,
        ]);
    }

    /**
     * Scope a query to only include bookings for a specific customer.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeForCustomer(Builder $builder, int $customerId): Builder
    {
        return $builder->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include bookings for a specific trip.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeForTrip(Builder $builder, int $tripId): Builder
    {
        return $builder->where('bus_trip_id', $tripId);
    }

    /**
     * Scope a query to only include bookings for a specific company.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeForCompany(Builder $builder, int $companyId): Builder
    {
        return $builder->whereHas('busTrip', fn($q) => $q->where('travel_company_id', $companyId));
    }

    /**
     * Scope a query to only include bookings with specific status(es).
     *
     * @param Builder<BusTripBooking> $builder
     * @param string|array<string> $status
     * @return Builder<BusTripBooking>
     */
    public function scopeWithStatus(Builder $builder, string|array $status): Builder
    {
        return $builder->whereIn('booking_status', is_array($status) ? $status : [$status]);
    }

    /**
     * Scope a query to exclude bookings with specific status(es).
     *
     * @param Builder<BusTripBooking> $builder
     * @param string|array<string> $status
     * @return Builder<BusTripBooking>
     */
    public function scopeWithoutStatus(Builder $builder, string|array $status): Builder
    {
        return $builder->whereNotIn('booking_status', is_array($status) ? $status : [$status]);
    }

    /**
     * Scope a query to only include bookings with reserved seats.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeWithReservedSeats(Builder $builder): Builder
    {
        return $builder->whereNotNull('reserved_seat_numbers')
            ->where('reserved_seat_numbers', '!=', '');
    }

    /**
     * Scope a query to only include canceled bookings.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeCanceled(Builder $builder): Builder
    {
        return $builder->where('booking_status', BookingStatusEnum::CANCELED->value)
            ->whereNotNull('canceled_at');
    }

    /**
     * Scope a query to exclude canceled bookings.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopeNotCanceled(Builder $builder): Builder
    {
        return $builder->where(function ($query): void {
            $query->where('booking_status', '!=', BookingStatusEnum::CANCELED->value)
                ->orWhereNull('canceled_at');
        });
    }

    /**
     * Scope a query to only include paid bookings.
     *
     * @param Builder<BusTripBooking> $builder
     * @return Builder<BusTripBooking>
     */
    public function scopePaid(Builder $builder): Builder
    {
        return $builder->where('booking_status', BookingStatusEnum::PAID->value);
    }
}
