<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppSetting.
 *
 * Represents an application setting that can store either a string or a numeric value.
 *
 * @property string|int|null $value         The actual value of the setting, either string or numeric.
 * @property string|null     $string_value  The string representation of the setting value.
 * @property int|null        $numeric_value The numeric representation of the setting value.
 *
 * @method static Builder|AppSetting newModelQuery() Create a new model query.
 * @method static Builder|AppSetting newQuery()      Create a new query.
 * @method static Builder|AppSetting query()         Create a query.
 *
 * @mixin \Eloquent
 */
final class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'string_value',
        'numeric_value',
    ];

    /**
     * Get the actual setting value, either string or numeric.
     *
     * @return int|string|null The value of the setting.
     */
    public function getValueAttribute(): int|string|null
    {
        return $this->numeric_value ?? $this->string_value;
    }

    /**
     * Set the appropriate value based on the type.
     *
     * @param int|string|null $value The value to set, either string or numeric.
     */
    public function setValueAttribute(int|string|null $value): void
    {
        $this->attributes['numeric_value'] = is_int($value) ? $value : null;
        $this->attributes['string_value'] = is_string($value) ? $value : null;
    }
}
