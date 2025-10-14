<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * Class Customer.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $birth_date
 * @property string      $national_id
 * @property string      $gender
 * @property string      $address
 * @property string      $mother_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<Customer> newModelQuery()
 * @method static Builder<Customer> newQuery()
 * @method static Builder<Customer> query()
 *
 * @property Collection<int, BusTripBooking>      $bookings
 * @property int|null                             $bookings_count
 * @property string|null                          $formatted_national_id
 * @property string|null                          $full_address
 * @property Collection<int, PersonalAccessToken> $tokens
 * @property int|null                             $tokens_count
 * @property User|null                            $user
 *
 * @method static Builder<Customer> whereAddress(string $value)
 * @method static Builder<Customer> whereBirthDate(string $value)
 * @method static Builder<Customer> whereCreatedAt(Carbon $value)
 * @method static Builder<Customer> whereGender(string $value)
 * @method static Builder<Customer> whereId(int $value)
 * @method static Builder<Customer> whereMotherName(string $value)
 * @method static Builder<Customer> whereNationalId(string $value)
 * @method static Builder<Customer> wherePreferredContactMethod(string $value)
 * @method static Builder<Customer> whereUpdatedAt(Carbon $value)
 * @method static Builder<Customer> whereUserId(int $value)
 *
 * @property string|null                          $birthdate
 * @property User|null                            $user
 *
 * @method static Builder<Customer> whereBirthdate(string $value)
 *
 * @mixin \Eloquent
 */
final class Customer extends Model
{
    use HasApiTokens;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birth_date',
        'national_id',
        'gender',
        'address',
        'mother_name',
    ];

    protected $with = ['user'];
    /**
     * The attributes that are required for a complete profile.
     *
     * @return array<int, string>
     */
    public static function getRequiredProfileFields(): array
    {
        return [
            'birth_date',
            'national_id',
            'gender',
            'address',
            'mother_name',
        ];
    }

    /**
     * Check if the customer profile is complete.
     */
    public function isProfileComplete(): bool
    {
        foreach (self::getRequiredProfileFields() as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the list of missing required fields.
     *
     * @return array<string> Array of field names that are missing or empty
     */
    public function getMissingFields(): array
    {
        $missingFields = [];
        foreach (self::getRequiredProfileFields() as $field) {
            if (empty($this->$field)) {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }

    /**
     * Get the user that owns the customer.
     *
     * @return BelongsTo<User, Customer>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bookings for the customer.
     *
     * @return HasMany<BusTripBooking>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(BusTripBooking::class, 'customer_id');
    }

    /**
     * Accessor for the formatted national ID.
     *
     * @return Attribute<string|null, string|null>
     */
    public function formattedNationalId(): Attribute
    {
        return Attribute::make(
            get: static fn(?string $value): ?string => $value ? strtoupper($value) : null,
            set: fn($value) => $value,
        );
    }

    /**
     * Accessor for the full address.
     *
     * @return Attribute<string|null, string|null>
     */
    public function fullAddress(): Attribute
    {
        return Attribute::make(
            get: static fn(?string $value): ?string => $value ? trim($value) : null,
            set: fn($value) => $value,
        );
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'birth_date' => 'date:Y-m-d',
            'national_id' => 'string',
            'gender' => 'string',
            'address' => 'string',
            'mother_name' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
