<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class RolePermission.
 *
 * @property int $id
 * @property int $permission_id
 * @property int $role_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder|RolePermission newModelQuery()
 * @method static Builder|RolePermission newQuery()
 * @method static Builder|RolePermission query()
 *
 * @property-read Permission $permission
 * @property-read Role $role
 *
 * @method static Builder<static>|RolePermission whereCreatedAt($value)
 * @method static Builder<static>|RolePermission whereId($value)
 * @method static Builder<static>|RolePermission wherePermissionId($value)
 * @method static Builder<static>|RolePermission whereRoleId($value)
 * @method static Builder<static>|RolePermission whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';

    protected $fillable = [
        'permission_id',
        'role_id',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    protected function casts(): array
    {
        return [
            'permission_id' => 'integer',
            'role_id' => 'integer',
        ];
    }
}
