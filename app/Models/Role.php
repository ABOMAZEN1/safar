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
 * Class Role.
 *
 * @property int $id
 * @property string $role_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 *
 * @property Collection<int, RolePermission> $permissions
 * @property int|null $permissions_count
 *
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereRoleName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 *
 * @property string $name
 * @property string $display_name
 *
 * @method static Builder|Role whereDisplayName($value)
 * @method static Builder|Role whereName($value)
 *
 * @mixin \Eloquent
 */
final class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'role_name',
    ];

    /**
     * Get the permissions for the role.
     *
     * @return HasMany<RolePermission>
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Get the casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role_name' => 'string',
        ];
    }
}
