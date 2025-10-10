<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\BusTripStatusEnum;
use App\Models\BusTrip;
use App\Models\Customer;
use App\Models\BusTripBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusTripBooking>
 */
final class BusTripBookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BusTripBooking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $busTrip = BusTrip::inRandomOrder()->first() ?? BusTrip::factory()->create();
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        $reservedSeatCount = fake()->numberBetween(1, 3);
        $totalPrice = $busTrip->ticket_price * $reservedSeatCount;

        // Generate random seat numbers based on the reservedSeatCount
        $availableSeats = range(1, $busTrip->number_of_seats);
        $seatNumbers = fake()->randomElements($availableSeats, $reservedSeatCount);
        sort($seatNumbers);
        $seatNumbersString = implode(',', $seatNumbers);

        return [
            'bus_trip_id' => $busTrip->id,
            'customer_id' => $customer->id,
            'reserved_seat_count' => $reservedSeatCount,
            'reserved_seat_numbers' => $seatNumbersString,
            'total_price' => $totalPrice,
            'booking_status' => fake()->randomElement([
                BusTripStatusEnum::ACTIVE->value,
                BusTripStatusEnum::CANCELED->value,
                BusTripStatusEnum::COMPLETED->value
            ]),
            'is_departure_confirmed' => fake()->boolean(),
            'is_return_confirmed' => fake()->boolean(),
            'qr_code_path' => null, // This will be generated later if needed
        ];
    }

    /**
     * Indicate that the booking is in 'active' status.
     */
    public function active(): self
    {
        return $this->state(fn(): array => [
            'booking_status' => BusTripStatusEnum::ACTIVE->value,
            'is_departure_confirmed' => false,
            'is_return_confirmed' => false,
        ]);
    }

    /**
     * Indicate that the booking is in 'canceled' status.
     */
    public function canceled(): self
    {
        return $this->state(fn(): array => [
            'booking_status' => BusTripStatusEnum::CANCELED->value,
        ]);
    }

    /**
     * Indicate that the booking is in 'completed' status.
     */
    public function completed(): self
    {
        return $this->state(fn(): array => [
            'booking_status' => BusTripStatusEnum::COMPLETED->value,
            'is_departure_confirmed' => true,
            'is_return_confirmed' => true,
        ]);
    }
}
