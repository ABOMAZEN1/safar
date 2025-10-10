<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class SuperAdmin.
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder|SuperAdmin newModelQuery()
 * @method static Builder|SuperAdmin newQuery()
 * @method static Builder|SuperAdmin query()
 *
 * @property-read User $user
 *
 * @method static Builder|SuperAdmin whereCreatedAt($value)
 * @method static Builder|SuperAdmin whereId($value)
 * @method static Builder|SuperAdmin whereUpdatedAt($value)
 * @method static Builder|SuperAdmin whereUserId($value)
 *
 * @mixin \Eloquent
 */
final class SuperAdmin extends Model
{
    use HasFactory;

    protected $table = 'super_admins';

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }
}
