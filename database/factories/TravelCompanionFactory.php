<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BusTripBooking;
use App\Models\TravelCompanion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TravelCompanion>
 */
final class TravelCompanionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TravelCompanion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bus_trip_booking_id' => BusTripBooking::factory(),
            'companion_name' => fake()->name(),
        ];
    }

    /**
     * Configure the model factory to associate with an existing booking.
     */
    public function forBooking(int $bookingId): self
    {
        return $this->state(fn(): array => [
            'bus_trip_booking_id' => $bookingId,
        ]);
    }
}
