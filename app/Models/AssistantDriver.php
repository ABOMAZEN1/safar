<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class AssistantDriver.
 *
 * @property int $id
 * @property int $user_id
 * @property int $travel_company_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read TravelCompany $travelCompany
 * @property-read User $user
 *
 * @mixin \Eloquent
 */
final class AssistantDriver extends Model
{
    use HasFactory;

    protected $table = 'assistant_drivers';

    protected $fillable = [
        'user_id',
        'travel_company_id',
    ];

    /**
     * Get the user that owns the assistant driver.
     *
     * @return BelongsTo<User, AssistantDriver>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the company that owns the assistant driver.
     *
     * @return BelongsTo<TravelCompany, AssistantDriver>
     */
    public function travelCompany(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class, 'travel_company_id');
    }
}
