<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Permission.
 *
 * @property int         $id
 * @property string      $permission_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission query()
 * @method static Builder|Permission whereCreatedAt(Carbon $value)
 * @method static Builder|Permission whereId(int $value)
 * @method static Builder|Permission wherePermissionName(string $value)
 * @method static Builder|Permission whereUpdatedAt(Carbon $value)
 * @method static Builder|Permission whereKey(string $value)
 * @method static Builder|Permission whereTableName(string|null $value)
 *
 * @mixin \Eloquent
 */
final class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'permission_name',
    ];

    /**
     * Get the casted attributes for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permission_name' => 'string',
        ];
    }
}
