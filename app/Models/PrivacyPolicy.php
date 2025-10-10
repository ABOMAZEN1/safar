<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class PrivacyPolicy.
 *
 * @property int         $id
 * @property string      $title
 * @property string      $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $formatted_title
 *
 * @method static Builder|PrivacyPolicy newModelQuery()
 * @method static Builder|PrivacyPolicy newQuery()
 * @method static Builder|PrivacyPolicy query()
 * @method static Builder|PrivacyPolicy whereCreatedAt(Carbon $value)
 * @method static Builder|PrivacyPolicy whereDescription(string $value)
 * @method static Builder|PrivacyPolicy whereId(int $value)
 * @method static Builder|PrivacyPolicy whereTitle(string $value)
 * @method static Builder|PrivacyPolicy whereUpdatedAt(Carbon $value)
 *
 * @mixin \Eloquent
 */
final class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $table = 'privacy_policies';

    protected $fillable = [
        'title',
        'description',
    ];

    /**
     * Get the formatted title as an uppercase string.
     */
    public function formattedTitle(): Attribute
    {
        return Attribute::make(
            get: static fn(?string $value, array $attributes): ?string => isset($attributes['title']) ? strtoupper((string) $attributes['title']) : null,
        );
    }

    /**
     * Get the casted attributes for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
        ];
    }
}
