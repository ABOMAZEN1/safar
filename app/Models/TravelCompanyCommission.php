<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TravelCompanyCommissionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class TravelCompanyCommission.
 *
 * Represents the commission (fee or percentage) associated with a travel company.
 *
 * @property int           $id
 * @property float         $commission_amount
 * @property int           $travel_company_id
 * @property Carbon|null   $created_at
 * @property Carbon|null   $updated_at
 * @property TravelCompany $travelCompany
 *
 * @method static Builder<TravelCompanyCommission> newModelQuery()
 * @method static Builder<TravelCompanyCommission> newQuery()
 * @method static Builder<TravelCompanyCommission> query()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(TravelCompanyCommissionObserver::class)]
final class TravelCompanyCommission extends Model
{
    protected $table = 'travel_company_commissions';

    protected $fillable = [
        'commission_amount',
        'travel_company_id',
    ];

    /**
     * Get the travel company that owns this commission.
     *
     * @return BelongsTo<TravelCompany, TravelCompanyCommission>
     */
    public function travelCompany(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class);
    }

    /**
     * Get the casts for the model's attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'commission_amount' => 'decimal:2',
            'travel_company_id' => 'integer',
        ];
    }
}
