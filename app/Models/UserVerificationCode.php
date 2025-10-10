<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class UserVerificationCode.
 *
 * Represents a verification code issued to a user with an expiration date.
 *
 * @property int         $id
 * @property int         $user_id
 * @property int         $code
 * @property Carbon      $expired_at
 * @property null|Carbon $used_at
 * @property int         $usage_count
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property User        $user
 * @property null|User   $creator
 *
 * @method static Builder<static>|UserVerificationCode newModelQuery()
 * @method static Builder<static>|UserVerificationCode newQuery()
 * @method static Builder<static>|UserVerificationCode query()
 *
 * @mixin \Eloquent
 */
final class UserVerificationCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expired_at',
        'used_at',
        'usage_count',
    ];

    /**
     * Get the user associated with this verification code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'code' => 'integer',
            'expired_at' => 'datetime',
            'used_at' => 'datetime',
            'usage_count' => 'integer',
        ];
    }
}
