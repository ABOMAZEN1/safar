<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class BusDriver.
 *
 * @property int $id
 * @property int $user_id
 * @property int $travel_company_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read TravelCompany $travelCompany
 * @property-read User $user
 *
 * @method static Builder|BusDriver newModelQuery()
 * @method static Builder|BusDriver newQuery()
 * @method static Builder|BusDriver query()
 * @method static Builder|BusDriver whereCompanyId(int $value)
 * @method static Builder|BusDriver whereCreatedAt(Carbon $value)
 * @method static Builder|BusDriver whereId(int $value)
 * @method static Builder|BusDriver whereUpdatedAt(Carbon $value)
 * @method static Builder|BusDriver whereUserId(int $value)
 *
 * @mixin \Eloquent
 */
final class BusDriver extends Model
{
    use HasFactory;

    protected $table = 'bus_drivers';

    protected $fillable = [
        'user_id',
        'travel_company_id',
    ];

    /**
     * Get the user that owns the driver.
     *
     * @return BelongsTo<User, BusDriver>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the company that owns the driver.
     *
     * @return BelongsTo<TravelCompany, BusDriver>
     */
    public function travelCompany(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class, 'travel_company_id');
    }
}
