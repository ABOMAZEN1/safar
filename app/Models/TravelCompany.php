<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class TravelCompany.
 *
 * @property int                          $id
 * @property int                          $user_id
 * @property string                       $company_name
 * @property string                       $contact_number
 * @property string                       $address
 * @property string                       $image_path
 * @property Carbon                       $created_at
 * @property Carbon                       $updated_at
 *
 * @method static Builder<TravelCompany> newModelQuery()
 * @method static Builder<TravelCompany> newQuery()
 * @method static Builder<TravelCompany> query()
 *
 * @property Collection<int, Bus>                     $buses
 * @property int|null                                 $buses_count
 * @property TravelCompanyCommission|null             $commission
 * @property Collection<int, TravelCompanyCommission> $commissions
 * @property int|null                                 $commissions_count
 * @property Collection<int, BusTrip>                 $trips
 * @property int|null                                 $trips_count
 * @property User                                     $user
 *
 * @method static Builder<TravelCompany> whereAddress(string $value)
 * @method static Builder<TravelCompany> whereCompanyName(string $value)
 * @method static Builder<TravelCompany> whereContactNumber(string $value)
 * @method static Builder<TravelCompany> whereCreatedAt(Carbon $value)
 * @method static Builder<TravelCompany> whereId(int $value)
 * @method static Builder<TravelCompany> whereImagePath(string $value)
 * @method static Builder<TravelCompany> whereStatus(bool $value)
 * @method static Builder<TravelCompany> whereUpdatedAt(Carbon $value)
 * @method static Builder<TravelCompany> whereUserId(int $value)
 *
 * @mixin \Eloquent
 */
final class TravelCompany extends Model
{
    use HasFactory;

    protected $table = 'travel_companies';

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_number',
        'address',
        'image_path',
    ];

    /**
     * Get the user associated with the travel company.
     *
     * @return BelongsTo<User, TravelCompany>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the bus trips associated with the travel company.
     *
     * @return HasMany<BusTrip>
     */
    public function busTrips(): HasMany
    {
        return $this->hasMany(BusTrip::class, 'travel_company_id');
    }

    /**
     * Get the buses associated with the travel company.
     *
     * @return HasMany<Bus>
     */
    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class, 'travel_company_id');
    }

    /**
     * Get all commissions for the travel company.
     *
     * @return HasMany<TravelCompanyCommission>
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(TravelCompanyCommission::class);
    }

    /**
     * Accessor for the image path.
     *
     * @return Attribute<string, string>
     */
    public function imagePath(): Attribute
    {
        return Attribute::make(
            get: static fn(?string $value): ?string => $value ? url('storage/' . $value) : null,
            set: static fn(?string $value): ?string => $value,
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'company_name' => 'string',
            'contact_number' => 'string',
            'address' => 'string',
            'image_path' => 'string',
        ];
    }
}
