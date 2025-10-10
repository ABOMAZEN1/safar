<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerComplaint.
 *
 * Represents a complaint submitted by a customer.
 *
 * @property int         $id
 * @property int         $customer_id
 * @property string      $complaint_description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Customer    $customer
 *
 * @method static Builder<CustomerComplaint> newModelQuery()
 * @method static Builder<CustomerComplaint> newQuery()
 * @method static Builder<Customer> query()
 * @method static Builder<CustomerComplaint> whereId(int $value)
 * @method static Builder<CustomerComplaint> whereCustomerId(int $value)
 * @method static Builder<CustomerComplaint> whereComplaintDescription(string $value)
 * @method static Builder<CustomerComplaint> whereCreatedAt(Carbon $value)
 * @method static Builder<CustomerComplaint> whereUpdatedAt(Carbon $value)
 *
 * @mixin \Eloquent
 */
final class CustomerComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'complaint_description',
    ];

    /**
     * Get the customer who submitted the complaint.
     *
     * @return BelongsTo<Customer, CustomerComplaint>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Cast attributes to native types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'customer_id' => 'integer',
            'complaint_description' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
