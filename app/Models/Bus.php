<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Bus.
 *
 * @property int           $id
 * @property int           $bus_type_id
 * @property int           $travel_company_id
 * @property int           $capacity
 * @property string        $details
 * @property Carbon|null   $created_at
 * @property Carbon|null   $updated_at
 * @property-read BusType       $busType
 * @property-read TravelCompany $travelCompany
 *
 * @method static Builder|Bus newModelQuery()
 * @method static Builder|Bus newQuery()
 * @method static Builder|Bus query()
 * @method static Builder|Bus whereBusTypeId(int $value)
 * @method static Builder|Bus whereCapacity(int $value)
 * @method static Builder|Bus whereTravelCompanyId(int $value)
 * @method static Builder|Bus whereCreatedAt(Carbon $value)
 * @method static Builder|Bus whereDetails(string $value)
 * @method static Builder|Bus whereId(int $value)
 * @method static Builder|Bus whereUpdatedAt(Carbon $value)
 *
 * @mixin \Eloquent
 */
final class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_type_id',
        'travel_company_id',
        'capacity',
        'details',
    ];

    /**
     * Get the travel company associated with the bus.
     *
     * @return BelongsTo<TravelCompany, Bus>
     */
    public function travelCompany(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class, 'travel_company_id');
    }

    /**
     * Get the bus type associated with the bus.
     *
     * @return BelongsTo<BusType, Bus>
     */
    public function busType(): BelongsTo
    {
        return $this->belongsTo(BusType::class, 'bus_type_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }
}
