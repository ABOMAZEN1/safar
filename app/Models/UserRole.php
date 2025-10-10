<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_id
 * @property int         $role_id
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Role        $role
 * @property User        $user
 *
 * @method static Builder<static>|UserRole newModelQuery()
 * @method static Builder<static>|UserRole newQuery()
 * @method static Builder<static>|UserRole query()
 * @method static Builder<static>|UserRole whereCreatedAt($value)
 * @method static Builder<static>|UserRole whereId($value)
 * @method static Builder<static>|UserRole whereRoleId($value)
 * @method static Builder<static>|UserRole whereUpdatedAt($value)
 * @method static Builder<static>|UserRole whereUserId($value)
 *
 * @mixin \Eloquent
 */
final class UserRole extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'role_id' => 'integer',
        ];
    }
}
