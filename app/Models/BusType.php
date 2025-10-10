<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class BusType.
 *
 * @property int                  $id
 * @property string               $name
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property Collection<int, Bus> $buses
 * @property int|null             $buses_count
 *
 * @method static Builder|BusType newModelQuery()
 * @method static Builder|BusType newQuery()
 * @method static Builder|BusType query()
 * @method static Builder|BusType whereCreatedAt($value)
 * @method static Builder|BusType whereId($value)
 * @method static Builder|BusType whereName($value)
 * @method static Builder|BusType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class BusType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the buses associated with the bus type.
     *
     * @return HasMany<Bus>
     */
    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }

    /**
     * Get the name of the bus type.
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['name'];
    }

    /**
     * Set the name of the bus type.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'string',
        ];
    }
}
