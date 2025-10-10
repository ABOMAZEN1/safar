<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class TermsAndConditions.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder|TermsAndConditions newModelQuery()
 * @method static Builder|TermsAndConditions newQuery()
 * @method static Builder|TermsAndConditions query()
 * @method static Builder|TermsAndConditions whereCreatedAt($value)
 * @method static Builder|TermsAndConditions whereDescription($value)
 * @method static Builder|TermsAndConditions whereId($value)
 * @method static Builder|TermsAndConditions whereTitle($value)
 * @method static Builder|TermsAndConditions whereUpdatedAt($value)
 *
 * @property null|string $formatted_title
 *
 * @mixin \Eloquent
 */
final class TermsAndConditions extends Model
{
    use HasFactory;

    protected $table = 'terms_and_conditions';

    protected $fillable = [
        'title',
        'description',
    ];

    /**
     * Get the formatted title as uppercase.
     */
    public function formattedTitle(): Attribute
    {
        return Attribute::make(
            get: static fn(?string $value, array $attributes): ?string => isset($attributes['title']) ? strtoupper((string) $attributes['title']) : null,
        );
    }

    /**
     * Get the casting for the model attributes.
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
