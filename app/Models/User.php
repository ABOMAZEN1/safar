<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\UserTypeEnum;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;

/**
 * @property int                                                       $id
 * @property string                                                    $name
 * @property string                                                    $phone_number
 * @property string                                                    $password
 * @property null|Carbon                                               $verified_at
 * @property null|string                                               $remember_token
 * @property null|Carbon                                               $created_at
 * @property null|Carbon                                               $updated_at
 * @property Collection<int, UserVerificationCode>                     $verificationCodes
 * @property null|int                                                  $verificationCodes_count
 * @property null|TravelCompany                                        $company
 * @property null|Customer                                             $customer
 * @property Collection<int, Customer>                                 $customers
 * @property null|int                                                  $customers_count
 * @property null|BusDriver                                            $driver
 * @property DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property null|int                                                  $notifications_count
 * @property Collection<int, Role>                                     $roles
 * @property null|int                                                  $roles_count
 * @property Collection<int, PersonalAccessToken>                      $tokens
 * @property null|int                                                  $tokens_count
 *
 * @method static UserFactory          factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePhoneNumber($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereVerifiedAt($value)
 *
 * @property int|null    $role_id
 * @property int         $gender
 * @property string|null $phone_number
 * @property string|null $avatar
 * @property string|null $settings
 * @property string      $mobile
 * @property string|null $job
 * @property int         $is_admin
 * @property int|null    $leaves_number          6750:دقيقة تقابل 15 يوم اجازة,كل يوم اجازة يقابل450دقيقة
 * @property int|null    $default_leaves_number
 * @property int|null    $has_work_exception
 * @property int|null    $has_many_leaves_number
 * @property int|null    $is_external_employee
 * @property int|null    $balance
 * @property string|null $birthday
 *
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereBalance($value)
 * @method static Builder<static>|User whereBirthday($value)
 * @method static Builder<static>|User whereDefaultLeavesNumber($value)
 * @method static Builder<static>|User wherePhoneNumber($value)
 * @method static Builder<static>|User whereGender($value)
 * @method static Builder<static>|User whereHasManyLeavesNumber($value)
 * @method static Builder<static>|User whereHasWorkException($value)
 * @method static Builder<static>|User whereIsAdmin($value)
 * @method static Builder<static>|User whereIsExternalEmployee($value)
 * @method static Builder<static>|User whereJob($value)
 * @method static Builder<static>|User whereLeavesNumber($value)
 * @method static Builder<static>|User whereMobile($value)
 * @method static Builder<static>|User whereRoleId($value)
 * @method static Builder<static>|User whereSettings($value)
 *
 * @property int|null $verification_codes_count
 *
 * @mixin \Eloquent
 */
final class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'type',
        'password',
        'verified_at',
        'profile_image_path',
        'firebase_token',
        'firebase_token_updated_at',
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    private string $username = 'phone_number';

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        foreach ($this->roles as $role) {
            if ($role->role_name === UserTypeEnum::SUPER_ADMIN->value) {
                return true;
            }
        }

        return false;
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(TravelCompany::class);
    }

    public function driver(): HasOne
    {
        return $this->hasOne(BusDriver::class);
    }

    public function verificationCodes(): HasMany
    {
        return $this->hasMany(UserVerificationCode::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function password(): Attribute
    {
        return Attribute::make(
            get: fn(string $value): string => $value,
            set: fn(string $value): string => Hash::needsRehash($value) ? Hash::make($value) : $value,
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
            'password' => 'hashed',
            'profile_image_path' => 'string',
            'firebase_token_updated_at' => 'datetime',
        ];
    }
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
    /**
     * Accessor for the profile image path.
     */
    public function profileImagePath(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value): ?string => $value ? url('storage/' . $value) : null,
            set: fn(?string $value): ?string => $value,
        );
    }

    /**
     * Check if the user is a Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('role_name', UserTypeEnum::SUPER_ADMIN->value)->exists();
    }

    /**
     * Check if the user has Super Admin role
     */
    public function hasSuperAdminRole(): bool
    {
        foreach ($this->roles as $role) {
            if ($role->role_name === UserTypeEnum::SUPER_ADMIN->value) {
                return true;
            }
        }
        return false;
    }

    public function assistantDriver(): HasOne
    {
        return $this->hasOne(AssistantDriver::class);
    }
}
